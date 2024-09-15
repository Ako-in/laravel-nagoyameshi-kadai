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
        $response->assertStatus(200);
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
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        // 管理者としてログイン
        $this->actingAs($admin, 'admin');

        // 管理者が店舗一覧ページにアクセスしようとする
        $response = $this->get(route('restaurants.index'));

        $response->assertRedirect(route('admin.home'));

    }
}
