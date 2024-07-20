<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    // indexアクション（店舗一覧ページ）
    // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_not_login_adminuser_cannot_access_to_restaurants_index(): void
        // OK!!!未ログインadminのユーザーは管理者側の店舗一覧ページにアクセスできない
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
        $admin->save();
        // 既存の管理者アカウントを確認して存在しない場合のみ作成
        // $admin = Admin::firstOrCreate(
        //     ['email' => 'admin@example.com'],
        //     ['password' => Hash::make('nagoyameshi')]
        // );
        $response = $this->actingAs($admin,'admin')->get('admin/restaurants/index');
        $response->assertStatus(200);
    }

    // showアクション（店舗詳細ページ）
    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる

    public function test_not_login_user_cannot_access_to_restaurants_show(): void
    // OK!!! ログインしていないユーザー:管理者側の会員詳細ページにアクセスNG
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/restaurants/{1}');//アドミン会員詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_login_cannot_access_to_restaurants_show(): void
    // OK!!! ログイン済みの一般ユーザー:管理者側の店舗詳細ページにアクセスNG
    {
        // テストユーザー作成
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/login');//アドミンページにアクセス
        $response = $this->get('admin/restaurants/{1}');//アドミン詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_show(): void
    // ログイン済みの管理者:管理者側の店舗詳細ページにアクセスOK
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        // $admin = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
        // データを一件とってきて確認する
        // $response = $this->actingAs($admin,'admin')->get('admin/restaurants/{1}');
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant));
        $response->assertStatus(200);
        $response->assertSee($restaurant->name);
    }

    // createアクション（店舗登録ページ）
    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる

    public function test_not_login_adminuser_cannot_access_to_restaurants_create(): void
        // OK!!!未ログインadminのユーザーは管理者側の店舗登録ページにアクセスできない
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/restaurants/create');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_login_cannot_access_to_restaurants_create(): void
         // ログイン済みの管理者:管理者側の店舗登録ページにアクセスできない
        {
        // テストユーザー作成
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/login');//アドミンページにアクセス
        $response = $this->get('admin/restaurants/{1}');//アドミン詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
       
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

    // // storeアクション（店舗登録機能）
    // // 未ログインのユーザーは店舗を登録できない
    // // ログイン済みの一般ユーザーは店舗を登録できない
    // // ログイン済みの管理者は店舗を登録できる

    public function test_not_login_adminuser_cannot_access_to_restaurants_store(): void
        // OK!!!未ログインadminのユーザーは管理者側の店舗登録できない
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $restaurant = Restaurant::factory()->create();
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/restaurants/store');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_restaurants_store(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側の店舗登録できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/restaurants/store');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_store(): void
    // ログイン済みの管理者:管理者側の店舗登録できる
    {
        //admin用のユーザーを作成
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //レストラン用のフォームデータ作成
        $restaurant = Restaurant::factory()->create();
        //作成したユーザーでアクセス
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.store', $restaurant));
        // $response->assertStatus(200);
        // $this->assertDatabaseHas('restaurants',$restaurant);
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
        // $response->assertRedirect(route('admin.restaurants.index'));
        // $response->assertStatus(302);

        // $response->assertSee($restaurant->name);

    }

    // // editアクション（店舗編集ページ）
    // // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    // // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    // // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_not_login_user_cannot_access_to_restaurants_edit(): void
    // OK!!! ログインしていないユーザー:管理者側の店舗編集ページにアクセスNG
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/restaurants/{1}/edit');//アドミン会員詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_login_cannot_access_to_restaurants_edit(): void
    // OK!!! ログイン済みの一般ユーザー:管理者側の店舗編集ページにアクセスNG
    {
        // テストユーザー作成
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        $response = $this->get('admin/login');//アドミンページにアクセス
        $response = $this->get('admin/restaurants/{1}/edit');//アドミン編集ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_restaurants_edit(): void
    // ログイン済みの管理者:管理者側の店舗編集ページにアクセスOK
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //レストラン用のフォームデータ作成
        $restaurant = Restaurant::factory()->create();
        
        //作成したユーザーでアクセス
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant));
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
    }

    // // updateアクション（店舗更新機能）
    // // 未ログインのユーザーは店舗を更新できない
    // // ログイン済みの一般ユーザーは店舗を更新できない
    // // ログイン済みの管理者は店舗を更新できる

    public function test_not_login_adminuser_cannot_access_to_restaurants_update(): void
        // OK!!!未ログインadminのユーザーは管理者側の店舗更新できない
    {
        $user = User::factory()->create();
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.edit',$restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_restaurants_update(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側の店舗更新へアクセスできない
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

    // // destroyアクション（店舗削除機能）
    // // 未ログインのユーザーは店舗を削除できない
    // // ログイン済みの一般ユーザーは店舗を削除できない
    // // ログイン済みの管理者は店舗を削除できる
    public function test_not_login_adminuser_cannot_access_to_restaurants_destroy(): void
        // OK!!!未ログインadminのユーザーは管理者側の店舗削除できない
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->delete(route('admin.restaurants.destroy',$restaurant));
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
    // Ok!!! ログイン済みの一般ユーザーは管理者側の店舗削除できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->delete(route('admin.restaurants.destroy',$restaurant));
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
        $response->assertRedirect(route('admin.login'));
       
    }
    
    public function test_login_adminuser_can_access_to_restaurants_destroy(): void
    // ログイン済みの管理者:管理者側の店舗削除できる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        
        $this->actingAs($admin,'admin');
        $restaurant = Restaurant::factory(Restaurant::class)->create();
        $response = $this->delete(route('admin.restaurants.destroy',$restaurant));
        $this->assertDatabaseMissing('restaurants', [
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
            // $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
            $response->assertRedirect(route('admin.restaurants.index'));
    }


}

