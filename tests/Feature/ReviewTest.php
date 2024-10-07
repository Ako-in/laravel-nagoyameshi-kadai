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
use Tests\TestCase;

class ReviewTest extends TestCase
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

    // indexアクション（レビュー一覧ページ）
    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない

    public function test_not_login_user_cannot_access_to_subscribe_index(): void
    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.index',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_can_access_to_subscribe_index(): void
    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.index',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_subecribed_login_user_can_access_to_subscribe_index(): void
    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.index',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_subscribe_index(): void
    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        // $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // $user->newSubscripti   n('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.index',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }

    // createアクション（レビュー投稿ページ）
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_not_login_user_cannot_access_to_subscribe_create(): void
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.create',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_can_access_to_subscribe_create(): void
    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.create',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_subecribed_login_user_can_access_to_subscribe_create(): void
    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.create',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_subscribe_create(): void
    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        // $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // $user->newSubscripti   n('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.create',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（レビュー投稿機能）
    // 未ログインのユーザーはレビューを投稿できない
    // ログイン済みの無料会員はレビューを投稿できない
    // ログイン済みの有料会員はレビューを投稿できる
    // ログイン済みの管理者はレビューを投稿できない
    public function test_not_login_user_cannot_access_to_subscribe_store(): void
    // 未ログインのユーザーはレビューを投稿できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id'=>$restaurant->id,
            'user_id'=>$user->id,
        ]);

        $response = $this->post(route('restaurants.reviews.store',[
            'restaurant' => $restaurant->id,
            'review' => $review->id,
            'content'=>'テスト',
        ]));
        $this->assertDatabaseMissing('reviews',[
            'restaurant_id' => $restaurant->id,
            'user_id' => Null,
            'score' => $review->score,
            'content' => $review->content,
        ]);
        // 認証が必要なレスポンスを確認
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_subscribe_store(): void
    // ログイン済みの無料会員はレビューを投稿できない
    {
        $user = User::factory()->create(['subscribed' => false]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
         
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
            'score' => 1,
            'content' => 'テスト',
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        
        // $response = $this->post(route('restaurants.reviews.store',[
        //     'restaurant_id' => $restaurant->id,
        //     'user_id' => $user->id, 
        //     'score' => 1,
        //     'content' => 'テスト',
        // ]));
        
        // ログインページへのリダイレクトを確認
        // $response->assertRedirect(route('login'));
        // $this->seertDatabaseMissing('reviews',['restaurant' => $restaurant->id,'review'=>[
        //     'content'=>'テスト',
        //     'score'=>1,
        //     ]
        // ]);
        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', [
            'restaurant' => $restaurant->id, // RestaurantのIDを渡す
        ]), [
            'content' => 'これはテストレビューです。',
            'score'=> 1,
        ]);
        $response->assertStatus(403); // アクセスが禁止されていることを確認
        $this->assertDatabaseMissing('reviews',[
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'score' => 1,
            'content' => 'これはテストレビューです。',
        ]);
        // $response->assertStatus(403);
    }

    public function test_subecribed_login_user_can_access_to_subscribe_store(): void
    // ログイン済みの有料会員はレビューを投稿できる
    {
        $user = User::factory()->create(['subscribed' => true]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        // レビューを作成
        // $review = Review::factory()->create([
        //     'restaurant_id' => $restaurant->id,
        //     'user_id' => $user->id, 
        // ]);
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        
        // レビューのデータを作成
        $reviewData = [
            'content' => 'テスト',
            'score' => 5,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ];
        // レビューを投稿
        $response = $this->post(route('restaurants.reviews.store', $restaurant->id), $reviewData);
        // $response = $this->post(route('restaurants.reviews.store',[
        //     'restaurant' => $restaurant->id,
        //     'review' => $review->id,
        // ]));
        // $response->assertStatus(200);
        // データベースにレビューが存在することを確認
        $this->assertDatabaseHas('reviews',[
            'content' => $reviewData['content'],
            'score' => $reviewData['score'],
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);
        // リダイレクト
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    }

    public function test_login_adminuser_cannot_access_to_subscribe_store(): void
    // ログイン済みの管理者はレビューを投稿できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $this->actingAs($admin);
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $reviewData= [
            'score' => 3,
            'content' => 'テストTest',
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id, 
        ];
        
        $response = $this->post(route('restaurants.reviews.store',$restaurant->id), $reviewData);
        $this->assertDatabaseMissing('reviews',$reviewData);
        // $response->assertRedirect(route('admin.home'));
        $response->assertStatus(403);
    }

    // editアクション（レビュー編集ページ）
    // 1未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    // 2ログイン済みの無料会員はレビュー編集ページにアクセスできない
    // 3ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    // 4ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    // 5ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    public function test_not_login_user_cannot_access_to_subscribe_edit(): void
    // 1未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, // 管理者のuser_idを指定
        ]);
        $response = $this->get(route('restaurants.reviews.edit',[
            'restaurant' => $restaurant->id,
            'review' => $review->id,
        ]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_subscribe_edit(): void
    // 2ログイン済みの無料会員はレビュー編集ページにアクセスできない
    {
        $user = User::factory()->create(['subscribed'=>false]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        // $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        // $this->assertDatabaseMissing('reviews',[
        //     'id' => $review->id, // 存在しないことを確認したい属性
        // ]);
        $response->assertRedirect(route('subscription.create'));
        // $response->assertStatus(302);
    }

    public function test_subecribed_login_user_cannot_edit_otherusers_review(): void
    // 3ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $otherUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review= Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id, 
        ]);
        
       // 編集リクエストを送信
       $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));
        $response->assertStatus(403);
    }

    public function test_login_subscribed_user_can_edit_own_review(): void
    // 4ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
        ]);
        

        // レビュー取得リクエスト
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));
        // 200ステータスを期待
        $response->assertStatus(200);

    }

    public function test_login_aminduser_cannot_access_to_subscribe_edit(): void
    // 5ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id, 
        ]);
        // $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reviews.edit',[$restaurant->id, $review->id]));
        $response->assertRedirect(route('admin.home'));
    }

    // // updateアクション（レビュー更新機能）
    // // 1未ログインのユーザーはレビューを更新できない
    // // 2ログイン済みの無料会員はレビューを更新できない
    // // 3ログイン済みの有料会員は他人のレビューを更新できない
    // // 4ログイン済みの有料会員は自身のレビューを更新できる
    // // 5ログイン済みの管理者はレビューを更新できない

    public function test_not_login_user_cannot_access_to_subscribe_update(): void
    // 1未ログインのユーザーはレビューを更新できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, // 管理者のuser_idを指定
        ]);
        $response = $this->put(route('restaurants.reviews.update',[
            'restaurant' => $restaurant->id,
            'review' => $review->id,
        ]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_subscribe_update(): void
    // 2ログイン済みの無料会員はレビューを更新できない
    {
        $user = User::factory()->create([
            'subscribed' => false, // サブスクライブしていない状態にする
        ]);//テストユーザー作成
        // $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant->id, $review->id]));
        $response->assertStatus(403);
        // $response->assertRedirect(route('subscription.create'));
    }

    public function test_subecribed_login_user_cannot_update_otherusers_review(): void
    // 3ログイン済みの有料会員は他人のレビューを更新できない
    {
        $user = User::factory()->create([
            'subscribed'=>true,
        ]);
        $otherUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
            'score'=>5,
            'content'=>'テスト',
        ]);

        $review_new=[
            'restaurant_id'=>5,
            'user_id'=>$otherUser->id,
            'score'=>5,
            'content'=>'更新',
        ];
        // $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        // $otherUser->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
       
        $response = $this->put(route('restaurants.reviews.update',[$restaurant->id, $review_old]), $review_new);

        //     'restaurant' => $restaurant->id,
        //     'review' => $review->id,
        // ]),[
        //     'score' => 5,
        //     'content' => '編集', // 新しく追加
        //  ]);
        // $response = $this->get(route('restaurants.reviews.edit',[
        //     'restaurant' => $restaurant->id,
        //     'review' => $review->id,
        // ]));
        // $response->assertStatus(200);
        // $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews',$review_new);
        // $response->assertRedirect(route('subscription.create'));
        $response->assertStatus(403);
    }

    public function test_login_subscribed_user_can_update_own_review(): void
    // 4ログイン済みの有料会員は自身のレビューを更新できる
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);
        $review_new = [
            'content' => 'テスト更新', // ここがテストで期待する内容
            'score' => 5,
        ];
        
        $response = $this->put(route('restaurants.reviews.update', [
            'restaurant' => $restaurant->id,
            'review' => $review_old->id
        ]), $review_new);
        $this->assertDatabaseHas('reviews', [
            'id' => $review_old->id, 
            'content' => 'テスト更新',
            'score' => 5,
        ]);
        // $response->assertRedirect(route('restaurants.reviews.index',$restaurant->id));
        $response->assertStatus(200); // 更新が成功しているか確認
    }

    public function test_login_aminduser_cannot_update_to_review(): void
    // 5ログイン済みの管理者はレビューを更新できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id, 
        ]);
        // $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->put(route('restaurants.reviews.update',[
            'restaurant' => $restaurant->id,
            'review' => $review->id,
        ]));
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（レビュー削除機能）
    // 1未ログインのユーザーはレビューを削除できない
    // 2ログイン済みの無料会員はレビューを削除できない
    // 3ログイン済みの有料会員は他人のレビューを削除できない
    // 4ログイン済みの有料会員は自身のレビューを削除できる
    // 5ログイン済みの管理者はレビューを削除できない
    public function test_not_login_user_cannot_access_to_subscribe_delete(): void
    // 1未ログインのユーザーはレビューを削除できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, // 管理者のuser_idを指定
        ]);
        $response = $this->delete(route('restaurants.reviews.destroy',[
            'restaurant' => $restaurant->id,
            'review' => $review->id,
        ]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_subscribe_delete(): void
    // 2ログイン済みの無料会員はレビューを削除できない
    {
        $user = User::factory()->create([
            'subscribed' => false, // サブスクライブしていない状態にする
        ]);//テストユーザー作成
        // $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant->id, $review->id]));
        // $response->assertStatus(403);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    
        // $response->assertRedirect(route('subscription.create'));
    }

    public function test_subecribed_login_user_cannot_delete_otherusers_review(): void
    // 3ログイン済みの有料会員は他人のレビューを削除できない
    {
        $user = User::factory()->create(['subscribed'=>true,]);
        $this->actingAs($user);
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        // $review_old = Review::factory()->create([
        //     'restaurant_id' => $restaurant->id,
        //     'user_id'=>$user->id,
        //     // 'score'=>5,
        //     // 'content'=>'テスト',
        // ]);
        $review_new_other=Review::factory()->create([
            'restaurant_id'=>$restaurant->id,
            'user_id'=>$otherUser->id,
            'score'=>5,
            'content'=>'更新',
        ]);
        
        // $otherUser->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        //テストユーザーでログイン
        

        $this->assertDatabaseHas('reviews',[
            'id'=>$review_new_other->id,
            'restaurant_id'=>$restaurant->id,
            'user_id'=>$otherUser->id,
        ]);

        $this->actingAs($user);
        $response = $this->delete(route('restaurants.reviews.destroy',[$restaurant->id, $review_new_other->id]));
        $this->assertDatabaseMissing('reviews',[
            'id'=>$review_new_other->id,
            'restaurant_id'=>$restaurant->id,
            'user_id'=>$otherUser->id,
        ]);
        $response->assertStatus(302);
    }

    public function test_login_subscribed_user_can_delete_own_review(): void
    // 4ログイン済みの有料会員は自身のレビューを削除できる
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        // $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
            'score' => 1,
            'content' => 'テスト',
        ]);

        $review_new = [
            'score'=>5,
            'content'=>'テスト更新',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
        ];
        
        $response = $this->delete(route('restaurants.reviews.destroy',['restaurant' => $restaurant->id,
        'review' => $review_old->id]), $review_new);
        $this->assertDatabaseMissing('reviews',[
            'id' => $review_old->id, 
            'content' => 'テスト更新', 
            'score' => 5, 
        ]);
        $response->assertRedirect(route('restaurants.reviews.index',$restaurant->id));
    }

    public function test_login_aminduser_cannot_delete_to_review(): void
    // 5ログイン済みの管理者はレビューを削除できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id, 
        ]);
        // $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->delete(route('restaurants.reviews.destroy',[
            'restaurant' => $restaurant->id,
            'review' => $review->id,
        ]));
        $this->assertDatabaseHas('reviews',[
            'id' => $review->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id
        ]);
        $response->assertRedirect(route('admin.home'));
    }
}
