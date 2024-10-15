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
use App\Models\Term;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TermTest extends TestCase
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

    // 利用規約ページ
    // 1.未ログインのユーザーは会員側の利用規約ページにアクセスできる
    // 2.ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    // 3.ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    public function test_not_login_user_can_access_to_term_index(): void
    // 1.未ログインのユーザーは会員側の利用規約ページにアクセスできる
    {
        $term = Term::factory()->create();
        $response = $this->get(route('terms.index',$term));
        // $response->assertRedirect(); 
        $response->assertStatus(200);
    }

    public function test_login_user_can_access_to_term_index(): void
    // 2.ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $term = Term::factory()->create();
        $response = $this->actingAs($user)->get(route('terms.index',$term));//一覧ページにアクセス
        // $response->assertRedirect(); // ログインページにリダイレクトされるか確認
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_term_index(): void
    // 3.ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        // $admin = Admin::factory()->create();
       
        $this->actingAs($admin,'admin');//テストユーザーでログイン
        $term = Term::factory()->create();
        $response = $this->actingAs($admin,'admin')->get(route('terms.index'));
        // dd($response->headers->get('Location'));
        // $response->assertRedirect(route('admin.login'));
        $response->assertStatus(403);
    }
}
