<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HomeTest extends TestCase
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

    // 1.未ログインのユーザーは管理者側のトップページにアクセスできない
    // 2.ログイン済みの一般ユーザーは管理者側のトップページにアクセスできない
    // 3.ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_not_login_user_cannot_access_to_admin_index(): void
    // 1.未ログインのユーザーは管理者側のトップページにアクセスできない
    {
        $response = $this->get(route('admin.home'));
        // $response->assertRedirect(); 
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_admin_index(): void
    // 2.ログイン済みの一般ユーザーは管理者側のトップページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $response = $this->actingAs($user)->get(route('admin.home'));
        // $response->assertRedirect(); // ログインページにリダイレクトされるか確認
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_admin_index(): void
    // 3.ログイン済みの管理者は管理者側のトップページにアクセスできる
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $this->actingAs($admin,'admin');//テストユーザーでログイン
        $response = $this->actingAs($admin,'admin')->get(route('admin.home'));
        // $response->assertRedirect(route('admin.login'));
        $response->assertStatus(200);
    }

}
