<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware;
use App\Models\User;
use App\Models\Admin;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Favorite;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（お気に入り一覧ページ）
    // 1.未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
    // 2.ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
    // 3.ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
    // 4.ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない

    public function test_not_login_user_cannot_access_to_favorite_index(): void
    // 1.未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
    {
        // $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_favorite_index(): void
    // 2.ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subecribed_login_user_can_access_to_favorite_index(): void
    // 3.ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('favorites.index'));
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_favorite_index(): void
    // 4.ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（お気に入り追加機能）
    // 1.未ログインのユーザーはお気に入りに追加できない
    // 2.ログイン済みの無料会員はお気に入りに追加できない
    // 3.ログイン済みの有料会員はお気に入りに追加できる
    // 4.ログイン済みの管理者はお気に入りに追加できない
    public function test_not_login_user_cannot_access_to_favorite_store(): void
    // 1.未ログインのユーザーはお気に入りに追加できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // ログインしていない状態でPOSTリクエストを送信
        $response = $this->post(route('favorites.store', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_favorite_store(): void
    // 2.ログイン済みの無料会員はお気に入りに追加できない
    {
        $user = User::factory()->create(['subscribed' => false]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
       
        $restaurant = Restaurant::factory()->create();
        
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
       
        $response = $this->actingAs($user)->post(route('favorites.store', ['restaurant_id' => $restaurant->id]), [
            'restaurant_id' => $restaurant->id,
        ]);
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subecribed_login_user_can_access_to_favorite_store(): void
    // 3.ログイン済みの有料会員はお気に入りに追加できる
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create(['subscribed' => true]);//テストユーザー作成
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $response = $this->actingAs($user)->post(route('favorites.store', ['restaurant_id' => $restaurant->id]), [
            'restaurant_id' => $restaurant->id,
        ]);
        $response->assertRedirect();

        // データベースにお気に入りが追加されたことを確認
        $this->assertDatabaseHas('restaurant_user', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
    }

    public function test_login_adminuser_cannot_access_to_favorite_store(): void
    // 4.ログイン済みの管理者はお気に入りに追加できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $this->actingAs($admin);
        $restaurant = Restaurant::factory()->create();
        $response = $this->post(route('favorites.store',$restaurant), ['restaurant' => $restaurant->id,]);
        $response->assertRedirect(route('admin.login'));
    }

    // destroyアクション（お気に入り解除機能）
    // 1.未ログインのユーザーはお気に入りを解除できない
    // 2.ログイン済みの無料会員はお気に入りを解除できない
    // 3.ログイン済みの有料会員はお気に入りを解除できる
    // 4.ログイン済みの管理者はお気に入りを解除できない
    public function test_login_user_cannot_destroy_favorite(): void
    // 1.未ログインのユーザーはお気に入りを解除できない
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route('favorites.destroy',$restaurant), ['restaurant' => $restaurant->id,]);
        $response->assertRedirect(route('login'));

    }

    public function test_login_notsubscribed_user_cannot_destroy_favorite(): void
    // 2.ログイン済みの無料会員はお気に入りを解除できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // 有料プランに加入するユーザーとして設定
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard'); // プランに加入
        $this->actingAs($user);
        $response = $this->delete(route('favorites.destroy',$restaurant), ['restaurant' => $restaurant->id,]);

        $response->assertRedirect();
    }

    public function test_login_subscribed_user_can_destroy_favorite(): void
    // 3.ログイン済みの有料会員はお気に入りを解除できる
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan','price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_mastercard');
        $this->actingAs($user);
        $response = $this->delete(route('favorites.destroy',$restaurant), ['restaurant' => $restaurant->id,]);
        // データベースにお気に入りが追加されたことを確認
        $this->assertDatabaseMissing('restaurant_user', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        $response->assertRedirect();
    }

    public function test_login_adminuser_cannot_destroy_favorite(): void
    // 4.ログイン済みの管理者はお気に入りを解除できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin,'admin')->delete(route('favorites.destroy',$restaurant), ['restaurant' => $restaurant->id,]);
        $response->assertRedirect(route('admin.login'));
    }


}
