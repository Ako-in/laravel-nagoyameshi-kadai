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
use Stripe\Stripe;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;
use Stripe\PaymentMethod;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;
    use Billable;

    // createアクション（有料プラン登録ページ）

    public function test_not_login_user_cannot_access_to_subscribe_create(): void
    // 未ログインのユーザーは有料プラン登録ページにアクセスできない
    {
        $response = $this->get(route('subscription.create'));
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
        $response->assertRedirect('/subscription.edit');
    }

    public function test_login_adminuser_cannot_access_to_subscribe_create(): void
   // ログイン済みの管理者は有料プラン登録ページにアクセスできない
    { 
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        // Log::info($admin);

        $this->actingAs($admin);
        $response = $this->get(route('subscription.create'));
        $response->assertRedirect(route('admin.home'));
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
        // $response->assertRedirect('user.index');
    }

    public function test_login_subscribed_user_cannot_store_subscription(): void
    // ログイン済みの有料会員は有料プランに登録できない
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->post(route('subscription.store'),$request_parameter);
        $response->assertRedirect(route('user.index'));
    }

    public function test_login_adminuser_cannot_store_subscription(): void
    // ログイン済みの管理者は有料プランに登録できない
    {
        // Cashier::fake();  // Stripe API呼び出しをモック
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $this->actingAs($admin);
        $response = $this->get(route('subscription.create'));
        $response->assertRedirect(route('admin.home'));

    }

    // editアクション（お支払い方法編集ページ）
    // 未ログインのユーザーはお支払い方法編集ページにアクセスできない
    // ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    // ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    // ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    public function test_login_user_cannot_edit_subscription(): void
    // 未ログインのユーザーはお支払い方法編集ページにアクセスできない
    {
        $response = $this->post(route('subscription.store'));
        $response->assertRedirect(route('login'));
    }

    public function test_login_notsubscribed_user_cannot_edit_subscription(): void
    // ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertRedirect('subscription.create');
    }

    public function test_login_subscribed_user_can_edit_subscription(): void
    // ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard');
        $this->actingAs($user);//テストユーザーでログイン
        $this->assertTrue($user->subscribed('premium_plan'));
        $response = $this->get(route('subscription.edit'));
    }

    public function test_login_adminuser_cannot_edit_subscription(): void
    // ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $response = $this->actingAs($admin,'admin')->get(route('subscription.edit'));
        $response->assertStatus(302);
        // $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（お支払い方法更新機能）
    // 未ログインのユーザーはお支払い方法を更新できない
    // ログイン済みの無料会員はお支払い方法を更新できない
    // ログイン済みの有料会員はお支払い方法を更新できる
    // ログイン済みの管理者はお支払い方法を更新できない
    public function test_login_user_cannot_update_subscription(): void
    // 未ログインのユーザーはお支払い方法を更新できない
    {
        $user = User::factory()->create();
        $old_payment_method = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->patch(route('subscription.update',$old_payment_method),$request_parameter);
        $response->assertRedirect(route('login'));

    }

    public function test_login_notsubscribed_user_cannot_update_subscription(): void
    // ログイン済みの無料会員はお支払い方法を更新できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $user->createAsStripeCustomer(); // ユーザーをStripe顧客として登録
        $old_payment_method = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $this->assertFalse($user->subscribed('premium_plan'));

        $response = $this->actingAs($user)->patch(route('subscription.update',$old_payment_method),$request_parameter);
        $response->assertStatus(302);
    }

    public function test_login_subscribed_user_can_update_subscription(): void
    // ログイン済みの有料会員はお支払い方法を更新できる
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard');

        $old_payment_method = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->patch(route('subscription.update',$old_payment_method),$request_parameter);
        $this->assertNotEquals($old_payment_method, $request_parameter);
        $response->assertRedirect(route('user.index'));
    }


    public function test_login_adminuser_cannot_update_subscription(): void
    // ログイン済みの管理者はお支払い方法を更新できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $admin->createAsStripeCustomer(); // Stripe顧客を作成
        $old_payment_method = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($admin,'admin')->patch(route('subscription.update',$old_payment_method),$request_parameter);
        $response->assertStatus(302);
    }

    // cancelアクション（有料プラン解約ページ）
    // 未ログインのユーザーは有料プラン解約ページにアクセスできない
    // ログイン済みの無料会員は有料プラン解約ページにアクセスできない
    // ログイン済みの有料会員は有料プラン解約ページにアクセスできる
    // ログイン済みの管理者は有料プラン解約ページにアクセスできない
    public function test_login_user_cannot_cancel_subscription(): void
    // 未ログインのユーザーは有料プラン解約ページにアクセスできない
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->patch(route('subscription.update'));
        $response->assertRedirect(route('login'));
    }

    public function test_login_notsubscribed_user_cannot_cancel_subscription(): void
    // ログイン済みの無料会員は有料プラン解約ページにアクセスできない
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('subscription.cancel'));
        $response->assertRedirect('subscription.create');
    }

    public function test_login_subscribed_user_can_cancel_subscription(): void
    // ログイン済みの有料会員は有料プラン解約ページにアクセスできる
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard');
        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_cancel_subscription(): void
    // ログイン済みの管理者は有料プラン解約ページにアクセスできない
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin, 'admin')->get(route('subscription.cancel'));
        $response->assertStatus(302);
        // リダイレクトされることを確認
        // $response->assertRedirect(route('admin.home'));
    }


    // destroyアクション（有料プラン解約機能）
    // 未ログインのユーザーは有料プランを解約できない
    // ログイン済みの無料会員は有料プランを解約できない
    // ログイン済みの有料会員は有料プランを解約できる
    // ログイン済みの管理者は有料プランを解約できない

    public function test_login_user_cannot_destroy_subscription(): void
    // 未ログインのユーザーは有料プランを解約できない
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->patch(route('subscription.update'));
        $response->assertRedirect(route('login'));

    }

    public function test_login_notsubscribed_user_cannot_destroy_subscription(): void
    // ログイン済みの無料会員は有料プランを解約できない
    {
        $user = User::factory()->create();//テストユーザー作成
        // 有料プランに加入するユーザーとして設定
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard'); // プランに加入
        $this->actingAs($user);
        $response = $this->delete(route('subscription.destroy'));
        $response->assertRedirect(route('user.index'));

    }

    public function test_login_subscribed_user_can_destroy_subscription(): void
    // ログイン済みの有料会員は有料プランを解約できる
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard');
        $this->actingAs($user);
        $response = $this->delete(route('subscription.destroy'));
        $response->assertRedirect(route('user.index'));
    }

    public function test_login_adminuser_cannot_destroy_subscription(): void
    // ログイン済みの管理者は有料プランを解約できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $response = $this->delete(route('subscription.destroy'));
        $response->assertRedirect(route('login'));
    }
}