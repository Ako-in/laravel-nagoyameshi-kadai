<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;

class CategoryTest extends TestCase
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
    // indexアクション（カテゴリ一覧ページ）
    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_not_login_adminuser_cannot_access_to_categories_index(): void
        // OK!!!未ログインadminのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/categories/index');//店舗一覧ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_categories_index(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/categories/index');//店舗一覧ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_categories_index(): void
    // ログイン済みの管理者:管理者側のカテゴリ一覧ページにアクセスできる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin,'admin')->get('admin/categories/index');
        $response->assertStatus(200);
    }

    // storeアクション（カテゴリ登録機能）
    // 未ログインのユーザーはカテゴリを登録できない
    // ログイン済みの一般ユーザーはカテゴリを登録できない
    // ログイン済みの管理者はカテゴリを登録できる
    public function test_not_login_adminuser_cannot_access_to_categories_store(): void
        // OK!!!未ログインadminのユーザーは管理者側のカテゴリ登録できない
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $category = Category::factory()->create();
        $response = $this->get('admin/categories/store');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_categories_store(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側のカテゴリ登録できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $category = Category::factory()->create();
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/categories/store');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_categories_store(): void
    // ログイン済みの管理者:管理者側のカテゴリ登録できる
    {
        //admin用のユーザーを作成
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //レストラン用のフォームデータ作成
        $category = Category::factory()->create();
        //作成したユーザーでアクセス
        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.store', $category));
        $this->assertDatabaseHas(Category::class, [
            'name'=>$category->name,
        ]);

    }


    // updateアクション（カテゴリ更新機能）
    // 未ログインのユーザーはカテゴリを更新できない
    // ログイン済みの一般ユーザーはカテゴリを更新できない
    // ログイン済みの管理者はカテゴリを更新できる
    public function test_not_login_adminuser_cannot_access_to_categories_update(): void
        // OK!!!未ログインadminのユーザーは管理者側のカテゴリ更新できない
    {
        $user = User::factory()->create();
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $category = Category::factory()->create();
        $response = $this->get(route('admin.categories.edit',$category));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_categories_update(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側のカテゴリ更新へアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit',$category));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_categories_update(): void
    // ログイン済みの管理者:管理者側のカテゴリ更新できる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        //カテゴリ用のフォームデータ作成
        $category = Category::factory()->create();
        
        //作成したユーザーでアクセス
        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.edit', $category));
        $this->assertDatabaseHas(Category::class, [
            'name'=>$category->name,

        ]);
    }


    // destroyアクション（カテゴリ削除機能）
    // 未ログインのユーザーはカテゴリを削除できない
    // ログイン済みの一般ユーザーはカテゴリを削除できない
    // ログイン済みの管理者はカテゴリを削除できる
    public function test_not_login_adminuser_cannot_access_to_categories_destroy(): void
        // OK!!!未ログインadminのユーザーは管理者側のカテゴリ削除できない
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $response = $this->delete(route('admin.categories.destroy',$category));
        $this->assertDatabaseHas(Category::class, [
            'name'=>$category->name,
        ]);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_categories_destroy(): void
    // Ok!!! ログイン済みの一般ユーザーは管理者側のカテゴリ削除できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $category = Category::factory()->create();
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->delete(route('admin.categories.destroy',$category));
        $this->assertDatabaseHas(Category::class, [
            'name'=>$category->name,
        ]);
        $response->assertRedirect(route('admin.login'));
       
    }
    
    public function test_login_adminuser_can_access_to_categories_destroy(): void
    // ログイン済みの管理者:管理者側のカテゴリ削除できる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        
        $this->actingAs($admin,'admin');
        $category = Category::factory(Category::class)->create();
        $response = $this->delete(route('admin.categories.destroy',$category));
        $this->assertDatabaseMissing('categories', [
            'name'=>$category->name,
            ]);
            $response->assertRedirect(route('admin.categories.index'));
    }
}
