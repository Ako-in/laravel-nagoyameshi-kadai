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
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用のレストランデータを作成
        $this->restaurant = Restaurant::factory()->create();
    }
    // indexアクション（予約一覧ページ）
    // 1.未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    // 2.ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    // 3.ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    // 4.ログイン済みの管理者は会員側の予約一覧ページにアクセスできない


    public function test_not_login_user_cannot_access_to_reservation_index(): void
    // 1.未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    {
        // レストランデータを作成
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
        ]);
        $response = $this->get(route('reservation.index',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_reservation_index(): void
    // 2.ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();//テストユーザー作成
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('reservation.index',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_subecribed_login_user_can_access_to_reservation_index(): void
    // 3.ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();//テストユーザー作成
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
        ]);
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('reservation.index',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_reservation_index(): void
    // 4.ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$admin->id,
        ]);
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->get(route('reservation.index',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }


    // createアクション（予約ページ）
    // 1.未ログインのユーザーは会員側の予約ページにアクセスできない
    // 2.ログイン済みの無料会員は会員側の予約ページにアクセスできない
    // 3.ログイン済みの有料会員は会員側の予約ページにアクセスできる
    // 4.ログイン済みの管理者は会員側の予約ページにアクセスできない
    public function test_not_login_user_cannot_access_to_reservation_create(): void
    // 1.未ログインのユーザーは会員側の予約ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
        ]);
        $response = $this->get(route('restaurants.reservations.create',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_reservation_create(): void
    // 2.ログイン済みの無料会員は会員側の予約ページにアクセスできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reservations.create',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_subecribed_login_user_can_access_to_reservation_create(): void
    // 3.ログイン済みの有料会員は会員側の予約ページにアクセスできる
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
        ]);
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reservations.create',['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_login_adminuser_cannot_access_to_reservation_create(): void
    // 4.ログイン済みの管理者は会員側の予約ページにアクセスできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        // $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$admin->id,
        ]);
        // $user->newSubscripti   n('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $this->actingAs($admin);//テストユーザーでログイン
        $response = $this->get(route('restaurants.reservations.create',['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }
    // storeアクション（予約機能）
    // 1.未ログインのユーザーは予約できない
    // 2.ログイン済みの無料会員は予約できない
    // 3.ログイン済みの有料会員は予約できる
    // 4.ログイン済みの管理者は予約できない
    public function test_not_login_user_cannot_access_to_reservation_store(): void
    // 1.未ログインのユーザーは予約できない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        // $userExists = DB::table('users')->where('id', 20)->exists();
        // dd($userExists); // true が返れば存在する
        $reservationData = [
            'reservation_date' =>'2024-01-01',
            'reservation_time'=>'10:00',
            'number_of_people'=>10,
        ];
        $response = $this->post(route('restaurants.reservations.store',$restaurant),$reservationData);
        $this->assertDatabaseMissing('reservations',[
            'reserved_datetime'=>'2024-01-01 10:00',
            'number_of_people'=>10,
        ]);
        // 認証が必要なレスポンスを確認
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_access_to_reservation_store(): void
    // 2.ログイン済みの無料会員は予約できない
    {
        $user = User::factory()->create(['subscribed' => false]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
            // 'reserved_datetime'=>'2023-12-01 00:00:00',

            'number_of_people'=>50,
        ]);
        
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
       
        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', [
            'restaurant' => $restaurant->id, // RestaurantのIDを渡す
        ]), [
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ]);
        // $response->assertStatus(403); // アクセスが禁止されていることを確認
        $this->assertDatabaseMissing('reservations',[
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
            'reserved_datetime'=>'2023-12-01 00:00:00',
            // 'reservation_date' =>'2023-12-01',
            // 'reservation_time' =>'00:00:00',
            'number_of_people'=>50,
        ]);
        // $response->assertStatus(403);
        $response->assertRedirect(route('subscription.create'));
        
    }

    public function test_subecribed_login_user_can_access_to_reservation_store(): void
    // 3.ログイン済みの有料会員は予約できる
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create(['subscribed' => true]);//テストユーザー作成
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $reservationData = [
            'reservation_date' => '2023-12-01',
            'reservation_time' => '10:00',
            'number_of_people' => 50,
            // 'restaurant_id' => $restaurant->id,
            // 'user_id' => $user->id,
        ];
        // レビューを投稿
        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservationData);  

        // データベースにレビューが存在することを確認
        $this->assertDatabaseHas('reservations',[
            'reserved_datetime' =>  '2023-12-01 10:00:00',
            'number_of_people' => $reservationData['number_of_people'],
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);
        // $this->assertDatabaseHas('reservations',$reservationData);
        // リダイレクト
        $response->assertRedirect(route('reservation.index'));
    }

    public function test_login_adminuser_cannot_access_to_reservation_store(): void
    // 4.ログイン済みの管理者は予約できない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $this->actingAs($admin);
        $restaurant = Restaurant::factory()->create();
        $reservationData = [
            'restaurant_id' => $restaurant->id,
            'user_id'=>$admin->id,
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ];
      
        $response = $this->post(route('restaurants.reservations.store',$restaurant->id), $reservationData);
        $this->assertDatabaseMissing('reservations',$reservationData);
        $response->assertRedirect(route('admin.home'));
        // $response->assertStatus(403);
    }


    // destroyアクション（予約キャンセル機能）
    // 1.未ログインのユーザーは予約をキャンセルできない
    // 2.ログイン済みの無料会員は予約をキャンセルできない
    // 3.ログイン済みの有料会員は他人の予約をキャンセルできない
    // 4.ログイン済みの有料会員は自身の予約をキャンセルできる
    // 5.ログイン済みの管理者は予約をキャンセルできない
    public function test_not_login_user_cannot_delete_reservation(): void
    // 1.未ログインのユーザーは予約をキャンセルできない
    {
        $user = User::factory()->create();//テストユーザー作成
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ]);
        
        $response = $this->delete(route('reservation.destroy',[
            'restaurant' => $restaurant->id,
            'reservation' => $reservation->id,
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ]));
        $response->assertRedirect(route('login'));
    }

    public function test_notsubecribed_login_user_cannot_delete_reservation(): void
    // 2.ログイン済みの無料会員は予約をキャンセルできない
    {
        $user = User::factory()->create([
            'subscribed' => false, // サブスクライブしていない状態にする
        ]);//テストユーザー作成
        // $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$user->id,
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ]);
        // ここで有料プランに加入していないことを確認
        $this->assertFalse($user->subscribed('premium_plan'));
        $response = $this->actingAs($user)->delete(route('reservation.destroy', [$restaurant->id, $reservation->id]));
        // $response->assertStatus(403);
        $response->assertRedirect(route('reservation.index'));
    
        // $response->assertRedirect(route('subscription.create'));
    }

    public function test_subecribed_login_user_cannot_delete_otherusers_reservation(): void
    // 3.ログイン済みの有料会員は他人の予約をキャンセルできない
    {
        $user = User::factory()->create(['subscribed'=>true,]);
        $this->actingAs($user);
        $otherUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id'=>$otherUser->id,
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ]);
        $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
      
        $this->assertDatabaseHas('reservations',['id' => $reservation->id]);

        $response = $this->actingAs($user)->delete(route('reservation.destroy',[$restaurant->id, $reservation->id]));
        $this->assertDatabaseHas('reservations',['id'=>$reservation->id,]);
    }

    public function test_login_subscribed_user_can_delete_own_reservation(): void
    // 4.ログイン済みの有料会員は自身の予約をキャンセルできる
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        // $user->newSubscription('premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
            'reserved_datetime'=>'2023-12-01 00:00:00',
            'number_of_people'=>50,
        ]);

        $reservation_new = [
            'reserved_datetime'=>'2023-12-02 00:00:00',
            'number_of_people'=>10,
            'restaurant_id' => $restaurant->id,
            // 'user_id' => $user->id, 
        ];
        
        $response = $this->delete(route('reservation.destroy',['restaurant' => $restaurant->id,
        'reservation' => $reservation->id]), $reservation_new);
        $this->assertDatabaseMissing('reservations',[
            'id' => $reservation->id, 
            'reserved_datetime'=>'2023-12-02 00:00:00',
            'number_of_people'=>10,
        ]);
        $response->assertRedirect(route('reservation.index'));
    }

    public function test_login_aminduser_cannot_delete_reservation(): void
    // 5.ログイン済みの管理者は予約をキャンセルできない
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
            'is_admin' => true, // 管理者フラグを設定
        ]);
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id, 
        ]);
        $response = $this->actingAs($admin)->delete(route('reservation.destroy',[
            'restaurant' => $restaurant->id,
            'reservation' => $reservation->id,
        ]));
        $this->assertDatabaseHas('reservations',[
            'id' => $reservation->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $admin->id
        ]);
        $response->assertRedirect(route('admin.home'));
    }

}
