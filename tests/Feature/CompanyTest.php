<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware;
use App\Models\User;
use App\Models\Admin;
use App\Models\Subscribed;
use App\Models\NotSubscribed;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CompanyTest extends TestCase
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

    // 会社概要ページ
    // 1.未ログインのユーザーは会員側の会社概要ページにアクセスできる
    // 2.ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
    // 3.ログイン済みの管理者は会員側の会社概要ページにアクセスできない
    public function test_not_login_user_can_access_to_company_index(): void
    // 1.未ログインのユーザーは会員側の会社概要ページにアクセスできる
    {
        $company = Company::factory()->create();
        $response = $this->get(route('company.index',$company));
        // $response->assertRedirect(); 
        $response->assertStatus(200);
    }

    public function test_login_user_can_access_to_company_index(): void
    // 2.ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $company = Company::factory()->create();
        $response = $this->actingAs($user)->get(route('company.index',$company));
        // $response->assertRedirect(); // ログインページにリダイレクトされるか確認
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_company_index(): void
    // 3.ログイン済みの管理者は会員側の会社概要ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $company = Company::factory()->create();
        $this->actingAs($admin,'admin');//テストユーザーでログイン
        $response = $this->actingAs($admin,'admin')->get(route('company.index',$company));
        $response->assertRedirect(route('admin.login'));
        // $response->assertStatus(403);
    }
}
