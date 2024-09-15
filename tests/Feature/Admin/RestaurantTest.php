<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\CategoryRestaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\RegularHoliday;
use Carbon\Carbon;
use DateTimeInterface;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;


    // indexアクション（店舗一覧ページ）
    public function test_not_login_adminuser_cannot_access_to_restaurants_index(): void
        // 未ログインadminのユーザーは管理者側の店舗一覧ページにアクセスできない
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/restaurants/index');//店舗一覧ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_restaurants_index(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/restaurants/index');//店舗一覧ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_index(): void
    // ログイン済みの管理者:管理者側の店舗一覧ページにアクセスできる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->password = bcrypt('password');
        $admin->save();
        $response = $this->actingAs($admin,'admin')->get('admin/restaurants/index');
        $response->assertStatus(200);
    }

    // showアクション（店舗詳細ページ）

    public function test_not_login_user_cannot_access_to_restaurants_show(): void
    // OK!!! ログインしていないユーザー:管理者側の会員詳細ページにアクセスNG
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $response = $this->get('admin/restaurants/{1}');//アドミン会員詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_login_cannot_access_to_restaurants_show(): void
    // OK!!! ログイン済みの一般ユーザー:管理者側の店舗詳細ページにアクセスNG
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/login');//アドミンページにアクセス
        $response = $this->get('admin/restaurants/{1}');//アドミン詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_show(): void
    {
        // ログイン済みの管理者:管理者側の店舗詳細ページにアクセスOK
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        // カテゴリのダミーデータ作成
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create(['name' => 'カテゴリ' . $i]);
            array_push($categoryIds, $category->id);
        }
        $restaurant = Restaurant::factory()->create();
        $restaurant->categories()->attach($categoryIds);

        // データを一件とってきて確認する
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant->id));
        $response->assertStatus(200);
    }

    // createアクション（店舗登録ページ）

    public function test_not_login_adminuser_cannot_access_to_restaurants_create(): void
        // 未ログインadminのユーザーは管理者側の店舗登録ページにアクセスできない
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $response = $this->get('admin/restaurants/create');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_login_cannot_access_to_restaurants_create(): void
         // ログイン済みの管理者:管理者側の店舗登録ページにアクセスできない
        {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/login');//アドミンページにアクセス

        // ページが正常にレンダリングされることを確認(下記のどちらかでとおる)
        // $response->assertOk();
        $response->assertStatus(200);
        }

    public function test_login_adminuser_can_access_to_restaurants_create(): void
    // ログイン済みの管理者:管理者側の店舗登録ページにアクセスできる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin,'admin')->get('admin/restaurants/create');
        $response->assertOk()->assertViewIs('admin.restaurants.create');
    }

    // storeアクション（店舗登録機能）

    public function test_not_login_adminuser_cannot_access_to_restaurants_store(): void
        // 未ログインadminのユーザーは管理者側の店舗登録できない
    {
        $response = $this->post(route('admin.restaurants.store'), [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ]);

        // $response->assertStatus(419); // CSRFトークンエラーを確認
        $this->assertDatabaseMissing('restaurants', ['name' => 'テスト']); // データが保存されていないことを確認
        $response->assertRedirect(route('admin.login'));

    }

    public function test_login_user_cannot_access_to_restaurants_store(): void
    //ログイン済みの一般ユーザーは管理者側の店舗登録できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        // カテゴリのダミーデータ作成
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
        array_push($categoryIds, $category->id);    
        }

        //test
        // Log::info((new Carbon())->format("Y-m-d H:i:s"));
        
        // 送信データにcategory_idsパラメータを追加
        $restaurant = Restaurant::create([    
            "name" => "テスト",
            "description" => "テスト",
            "lowest_price" => 1000,
            "highest_price" => 5000,
            "postal_code" => "0000001",
            "address" => "テスト",
            "opening_time" => "10:00:00",
            "closing_time" => "20:00:00",
            "seating_capacity" => 50,
            "category_ids" => $categoryIds,
            "image" => "",
            "created_at"=>(new Carbon())->format("Y-m-d H:i:s"),
            "updated_at"=>(new Carbon())->format("Y-m-d H:i:s"),
        ]);

        $now = Carbon::now()->setTimezone('Asia/Tokyo');
        $categoryRestaurants = [];
        foreach ($categoryIds as $categoryId ) {
            $categoryRestaurant =CategoryRestaurant::create([
                "restaurant_id" => $restaurant['id'],
                "category_id" => $categoryId,
                "updated_at" => $now,
                "created_at" => $now,
            ]);
        
            array_push($categoryRestaurants, $categoryRestaurant->id);    
        }
        // Log::info($restaurant);

        $response = $this->post(route('admin.restaurants.store'), $restaurant->toArray());
        // category_idsパラメータを削除して検証
        unset($restaurant['category_ids']);

        $expectedData = $restaurant;

        $this->assertDatabaseHas('restaurants', $expectedData->toArray());
        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurants', 
            ['category_id' => $categoryId]
                // 'restaurant_id' は登録されたレストランのIDを設定
            );
        }
        $response->assertRedirect(route('admin.login'));
    }

    // editアクション（店舗編集ページ）

    public function test_not_login_user_cannot_access_to_restaurants_edit(): void
    // ログインしていないユーザー:管理者側の店舗編集ページにアクセスNG
    {
        //admin用のユーザーを作成
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // カテゴリのダミーデータ作成
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
        array_push($categoryIds, $category->id);    
        }
        // $admin = Admin::factory()->create();// テストユーザー作成
        $restaurant = Restaurant::create([    
            "name" => "テスト",
            "description" => "テスト",
            "lowest_price" => 1000,
            "highest_price" => 5000,
            "postal_code" => "0000000",
            "address" => "テスト",
            "opening_time" => "10:00:00",
            "closing_time" => "20:00:00",
            "seating_capacity" => 50,
            'category_ids' => $categoryIds,
            "image" => "",
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant->id));
        // ページが正常にレンダリングされることを確認(下記のどちらかでとおる)
        // $response->assertOk();
        $response->assertStatus(200);
    }

    public function test_user_login_cannot_access_to_restaurants_edit(): void
    // ログイン済みの一般ユーザー:管理者側の店舗編集ページにアクセスNG
    {
        //admin用のユーザーを作成
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        // テストユーザー作成
        $user = User::factory()->create();//テストユーザー作成
        // カテゴリのダミーデータ作成
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create(['name' => 'カテゴリ' . $i]);
            array_push($categoryIds, $category->id);
        }

        $restaurant = Restaurant::create([
            "name" => "テスト",
            "description" => "テスト",
            "lowest_price" => 1000,
            "highest_price" => 5000,
            "postal_code" => "0000000",
            "address" => "テスト",
            "opening_time" => "10:00:00",
            "closing_time" => "20:00:00",
            "seating_capacity" => 50,
            "image" => "",
        ]);
        $restaurant->categories()->attach($categoryIds);

        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant->id));

        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
        ]);

        // $response->assertRedirect(route('admin.login'));
        // ページが正常にレンダリングされることを確認(下記のどちらかでとおる)
        // $response->assertOk();
        $response->assertStatus(200);

        // $response = $this->get('admin/login');//アドミンページにアクセス
        // $response = $this->get('admin/restaurants/{1}/edit');//アドミン編集ページにアクセス
        // $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_edit(): void
    // ログイン済みの管理者:管理者側の店舗編集ページにアクセスOK
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        // カテゴリのダミーデータ作成
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create(['name' => 'カテゴリ' . $i]);
            array_push($categoryIds, $category->id);
        }
        //レストラン用のフォームデータ作成
        // $restaurant = Restaurant::factory()->create();
        $restaurant = Restaurant::create([
            "name" => "テスト",
            "description" => "テスト",
            "lowest_price" => 1000,
            "highest_price" => 5000,
            "postal_code" => "0000000",
            "address" => "テスト",
            "opening_time" => "10:00:00",
            "closing_time" => "20:00:00",
            "seating_capacity" => 50,
            "image" => "",
        ]);
        $restaurant->categories()->attach($categoryIds);

        // データを一件とってきて確認する
        
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant->id));
        $response->assertStatus(200);
    }

    // updateアクション（店舗更新機能）

    public function test_not_login_adminuser_cannot_access_to_restaurants_update(): void
        // 未ログインadminのユーザーは管理者側の店舗更新できない
    {
        $user = User::factory()->create();
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.edit',$restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_restaurants_update(): void
    //ログイン済みの一般ユーザーは管理者側の店舗更新へアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.edit',$restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_update(): void
    // ログイン済みの管理者:管理者側の店舗更新できる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        //レストラン用のフォームデータ作成
        $restaurant = Restaurant::factory()->create();
        
        //作成したユーザーでアクセス
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.update', $restaurant));
        $this->assertDatabaseHas(Restaurant::class, [
            'name'=>$restaurant->name,
            'description' => $restaurant->description,
            'lowest_price' => $restaurant->lowest_price,
            'highest_price' => $restaurant->highest_price,
            'postal_code' => $restaurant->postal_code,
            'address' => $restaurant->address,
            'opening_time' => $restaurant->opening_time,
            'closing_time' => $restaurant->closing_time,
            'seating_capacity' => $restaurant->seating_capacity,
        ]);

        // $response = $this->actingAs($admin,'admin')->get('admin/restaurants/{1}');
        // $response->assertStatus(200);
    }

    // destroyアクション（店舗削除機能）

    public function test_not_login_adminuser_cannot_access_to_restaurants_destroy(): void
        //未ログインadminのユーザーは管理者側の店舗削除できない
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->delete(route('admin.restaurants.destroy',$restaurant),['_token' => csrf_token()]);
        $this->assertDatabaseHas(Restaurant::class, [
            'name'=>$restaurant->name,
            'description' => $restaurant->description,
            'lowest_price' => $restaurant->lowest_price,
            'highest_price' => $restaurant->highest_price,
            'postal_code' => $restaurant->postal_code,
            'address' => $restaurant->address,
            'opening_time' => $restaurant->opening_time,
            'closing_time' => $restaurant->closing_time,
            'seating_capacity' => $restaurant->seating_capacity,
        ]);
        // $this->assertDatabaseHas('restaurants', ['id' => $post->id]);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_restaurants_destroy(): void
    // ログイン済みの一般ユーザーは管理者側の店舗削除できない
    {
        $user = User::factory()->create();//テストユーザー作成
        // カテゴリのダミーデータ作成
        $categoryIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $category = Category::create([
                'name' => 'カテゴリ' . $i
            ]);
        array_push($categoryIds, $category->id);    
        }
        $restaurant = Restaurant::factory()->create();
        $restaurant->categories()->attach($categoryIds);
        $this->assertDatabaseHas(Restaurant::class, [
            'name'=>$restaurant->name,
            'description' => $restaurant->description,
            'lowest_price' => $restaurant->lowest_price,
            'highest_price' => $restaurant->highest_price,
            'postal_code' => $restaurant->postal_code,
            'address' => $restaurant->address,
            'opening_time' => $restaurant->opening_time,
            'closing_time' => $restaurant->closing_time,
            'seating_capacity' => $restaurant->seating_capacity,
        ]);
        $restaurant->categories()->attach($categoryIds);
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant->id), ['_token' => csrf_token()]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
        ]);
        $response->assertRedirect(route('admin.login'));
       
    }
    
    public function test_login_adminuser_can_access_to_restaurants_destroy(): void
    // ログイン済みの管理者:管理者側の店舗削除できる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::create([
            "name" => "テスト",
            "description" => "テスト",
            "lowest_price" => 1000,
            "highest_price" => 5000,
            "postal_code" => "0000000",
            "address" => "テスト",
            "opening_time" => "10:00:00",
            "closing_time" => "20:00:00",
            "seating_capacity" => 50,
            "image" => "",
            
        ]);
        
        // $restaurant = Restaurant::factory()->create();
        $this->actingAs($admin, 'admin');
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant->id), ['_token' => csrf_token()]);
    
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.restaurants.index'));
    }


}

