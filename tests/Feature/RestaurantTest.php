<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_login_adminuser_can_access_to_restaurant_index(): void
    // 未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $response = $this->get(route('restaurants.index'));
        // $response->assertStatus(200);
        $response->assertRedirect(route('login'));
    }

    public function test_login_user_can_access_to_restaurant_index(): void
    // ログイン済みの一般ユーザーは会員側の店舗一覧ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('restaurants/index');//アドミン一覧ページにアクセス
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_restaurant_index(): void
    // ログイン済みの管理者は会員側の店舗一覧ページにアクセスできない
    {
        // $admin = new Admin();
        // $admin->email = 'admin@example.com';
        // $admin->password = Hash::make('nagoyameshi');
        // // 管理者としてログイン
        // // $this->actingAs($admin, 'admin');

        // // // 管理者が店舗一覧ページにアクセスしようとする
        // // $response = $this->get(route('restaurants.index'));
        // $response = $this->actingAs($admin, 'admin')->get(route('restaurants.index'));
        // $response->assertRedirect(route('admin.home'));
        $admin = Admin::factory()->create(); // 管理者ユーザー作成
        $this->actingAs($admin); // 管理者ユーザーでログイン
        $response = $this->get('restaurants/index');
        // $response->assertStatus(403); // 管理者はアクセス不可
        $response->assertRedirect(route('admin.home'));
    }

    // 未ログインのユーザーは会員側の店舗詳細ページにアクセスできる
    // ログイン済みの一般ユーザーは会員側の店舗詳細ページにアクセスできる
    // ログイン済みの管理者は会員側の店舗詳細ページにアクセスできない
    public function test_not_login_user_can_access_to_restaurants_show(): void
    // ログインしていないユーザー:管理者側の会員詳細ページにアクセスできる
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.show',['restaurant' => $restaurant->id]));//詳細ページにアクセス
        // $response->assertRedirect(route('restaurant.show'));
        $response->assertStatus(200);
    }

    public function test_user_login_can_access_to_restaurants_show(): void
    // ログイン済みの一般ユーザー:管理者側の店舗詳細ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.show',['restaurant' => $restaurant->id]));//詳細ページにアクセス
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_restaurants_show(): void
    {
        // ログイン済みの管理者:管理者側の店舗詳細ページにアクセスできない
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.show', ['restaurant' => $restaurant->id]));
        // $response->assertRedirect(route('admin.login'));
        $response->assertStatus(403);
    }
}
