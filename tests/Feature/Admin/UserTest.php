<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // 会員一覧ページ
    // 未ログインのユーザーは管理者側の会員一覧ページにアクセスできない
    // ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
    // ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる

    public function test_not_login_adminuser_cannot_access(): void
        // OK!!!未ログインadminのユーザーは管理者側の会員一覧ページにアクセスできない
        {
            $response = $this->get('login');//未ログインの状態で'login'にアクセス
            // $admin = Admin::factory()->create();// テストユーザー作成
            $response = $this->get('admin/users/index');//アドミン一覧ページにアクセス
            $response->assertRedirect(route('admin.login'));
        }

        public function test_login_user_cannot_access_to_admin_index(): void
        // Ok!!! ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
        {
            $user = User::factory()->create();//テストユーザー作成
            $this->actingAs($user);//テストユーザーでログイン
            // $response = $this->get('admin/login');//アドミンページにアクセス
            $response = $this->get('admin/users/index');//アドミン一覧ページにアクセス
            $response->assertRedirect(route('admin.login'));
        }

        public function test_login_adminuser_can_access_to_admin_index(): void
        // ログイン済みの管理者:管理者側の会員一覧ページにアクセスできる
        {
            $admin = new Admin();
            $admin->email= 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            $admin->save();
            
            $response = $this->actingAs($admin,'admin')->get('admin/users/index');
            $response->assertStatus(200);
        }



    // 会員詳細ページ
    // 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
    // ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない
    // ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる

    public function test_not_login_user_cannot_access_to_admin_show(): void
    // OK!!! ログインしていないユーザー:管理者側の会員詳細ページにアクセスNG
    {
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        // $admin = Admin::factory()->create();// テストユーザー作成
        $response = $this->get('admin/users/{1}');//アドミン会員詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_login_cannot_access_to_admin_show(): void
    // OK!!! ログイン済みの一般ユーザー:管理者側の会員詳細ページにアクセスNG
    {
        // テストユーザー作成
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('admin/login');//アドミンページにアクセス
        $response = $this->get('admin/users/{1}');//アドミン詳細ページにアクセス
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_admin_show(): void
    // ログイン済みの管理者:管理者側の会員詳細ページにアクセスOK
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        // データを一件とってきて確認する
        $response = $this->actingAs($admin,'admin')->get('admin/users/{1}');
        $response->assertStatus(200);
    
    }
}
