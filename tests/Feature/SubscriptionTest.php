<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Subscribed;
use App\Models\NotSubscribed;
use App\Http\Middleware;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    // createアクション（有料プラン登録ページ）

    public function test_not_login_user_cannot_access_to_subscribe_create(): void
    // 未ログインのユーザーは有料プラン登録ページにアクセスできない
    {
        // $user = User::factory()->create();
        // $this->actingAs($user);//テストユーザーでログイン   
        // $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $response = $this->get(route('subscription.create'));
        // dd($response->status(), $response->getContent());
        $response->assertRedirect(route('login'));
    }

    public function test_login_notsubecribed_user_can_access_to_subscribe_create(): void
    // ログイン済みの無料会員は有料プラン登録ページにアクセスできる
        {
        $user = User::factory()->create();//テストユーザー作成
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));

        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('subscription.create'));//有料プラン登録ページにアクセス

        // ページが正常にレンダリングされることを確認(下記のどちらかでとおる)
        // $response->assertOk();
        $response->assertStatus(200);
        }

    public function test_login_subscribed_user_cannot_access_to_subscribe_create(): void
    // ログイン済みの有料会員は有料プラン登録ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        // 有料プランに加入するユーザーとして設定
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('subscription.create'));//有料プラン登録ページにアクセス
        //アクセスできない、リダイレクト
        $response->assertRedirect(route('subscription.edit'));

    }

    public function test_login_adminuser_cannot_access_to_subscribe_create(): void
   // ログイン済みの管理者は有料プラン登録ページにアクセスできない
    { 
        // $admin = new Admin();
        // $admin->email= 'admin@example.com';
        // $admin->password = Hash::make('nagoyameshi');
        // $admin->save();
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        // Log::info($admin);

        $this->actingAs($admin);
        $response = $this->get(route('subscription.create'));
        $response->assertRedirect(route('login'));
    }

    // storeアクション（有料プラン登録機能）
    // 未ログインのユーザーは有料プランに登録できない
    // ログイン済みの無料会員は有料プランに登録できる
    // ログイン済みの有料会員は有料プランに登録できない
    // ログイン済みの管理者は有料プランに登録できない
    public function test_login_user_cannot_store_subscription(): void
    // 未ログインのユーザーは有料プランに登録できない
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->post(route('subscription.store'));
        // dd($response->status(), $response->getContent());
        $response->assertRedirect(route('login'));

    }

    public function test_login_notsubscribed_user_can_store_subscription(): void
    // ログイン済みの無料会員は有料プランに登録できる
    {
        $user = User::factory()->create();//テストユーザー作成
        // 有料プランに加入するユーザーとして設定
        $user->subscribed('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('subscription.create'));
        $this->assertFalse($user->subscribed('premium_plan'));
        $response->assertStatus(200);
        // $response->assertTrue()->$user->subscribed('premium_plan');
    }

    public function test_login_subscribed_user_cannot_store_subscription(): void
    // ログイン済みの有料会員は有料プランに登録できない
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN');
        // $this->actingAs($user);//テストユーザーでログイン
        
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->post(route('subscription.store'),$request_parameter);
        // $this->assertTrue($user->subscribed('premium_plan'));
        $response->assertRedirect(route('subscription.edit'));
    }

    public function test_login_adminuser_cannot_store_subscription(): void
    // ログイン済みの管理者は有料プランに登録できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $this->actingAs($admin);
        $response = $this->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('admin.login'));

    }

    // editアクション（お支払い方法編集ページ）
    // 未ログインのユーザーはお支払い方法編集ページにアクセスできない
    // ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    // ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    // ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    public function test_login_user_cannot_edit_subscription(): void
    // 未ログインのユーザーはお支払い方法編集ページにアクセスできない
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->post(route('subscription.store'));
        // dd($response->status(), $response->getContent());
        $response->assertRedirect(route('login'));

    }

    public function test_login_notsubscribed_user_cannot_edit_subscription(): void
    // ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        // 有料プランに加入するユーザーとして設定
        $user->subscribed('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('subscription.edit'));
        $this->assertFalse($user->subscribed('premium_plan'));
        $response->assertRedirect('subscription.create');
    }

    public function test_login_subscribed_user_can_edit_subscription(): void
    // ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    {
        $user = User::factory()->create();
        $user->subscribed('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN');

        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('subscription.edit'));
        $this->assertTrue($user->subscribed('premium_plan'));

        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_edit_subscription(): void
    // ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    {
        // $admin = User::factory()->create([
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('nagoyameshi'),
        //     'is_admin' => true, // 管理者フラグを設定
        // ]);
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $this->actingAs($admin);
        $response = $this->get(route('subscription.edit'),$request_parameter);
        $response->assertRedirect(route('admin.login'));
    }

    // updateアクション（お支払い方法更新機能）
    // 未ログインのユーザーはお支払い方法を更新できない
    // ログイン済みの無料会員はお支払い方法を更新できない
    // ログイン済みの有料会員はお支払い方法を更新できる
    // ログイン済みの管理者はお支払い方法を更新できない
    public function test_login_user_cannot_update_subscription(): void
    // 未ログインのユーザーはお支払い方法を更新できない
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->patch(route('subscription.update'));
        // dd($response->status(), $response->getContent());
        $response->assertRedirect(route('login'));

    }

    public function test_login_notsubscribed_user_cannot_update_subscription(): void
    // ログイン済みの無料会員はお支払い方法を更新できない
    {
        $user = User::factory()->create();//テストユーザー作成
        // 有料プランに加入するユーザーとして設定
        // $user->subscribed('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->patch(route('subscription.update'));
        $response->assertRedirect('subscription.create');
    }

    public function test_login_subscribed_user_can_update_subscription(): void
    // ログイン済みの有料会員はお支払い方法を更新できる
    {
        $user = User::factory()->create();
        $user->subscribed('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN');

        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->patch(route('subscription.update'));
        $this->assertTrue($user->subscribed('premium_plan'));

        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_update_subscription(): void
    // ログイン済みの管理者はお支払い方法を更新できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $this->actingAs($admin);
        $response = $this->patch(route('subscription.update'));

        $response->assertRedirect(route('admin.login'));

    }



    
}
