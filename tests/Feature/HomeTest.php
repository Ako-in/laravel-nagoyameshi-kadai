<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\CategoryRestaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\RegularHoliday;
use App\Models\Company;
use App\Models\Term;
use Carbon\Carbon;
use DateTimeInterface;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_login_adminuser_cannot_access_to_toppage(): void
    // 未ログインのユーザーは会員側のトップページにアクセスできる
    {
        $response = $this->get('/home'); // 未ログインで/homeにアクセス
        $response->assertRedirect(route('login')); // ログインページにリダイレクトされるか確認
    }
    
    public function test_login_user_cannot_access_to_toppage(): void
    // ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get('home');//アドミン一覧ページにアクセス
        $response->assertStatus(200); // 200ステータスコードが返ることを確認
    }

    public function test_login_adminuser_cannot_access_to_toppage():void
    // ログイン済みの管理者は会員側のトップページにアクセスできない
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
            
        $response = $this->actingAs($admin,'admin')->get('/');
        $response->assertRedirect(route('admin.home'));
    }

}
