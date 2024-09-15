<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;


class UserTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_not_login_adminuser_cannot_access_to_user_index(): void
        // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
        {
            $response = $this->get(route('user.index'));//一覧ページにアクセス
            $response->assertRedirect(route('login')); // ログインページにリダイレクトされるか確認
        }

        public function test_login_user_can_access_to_user_index(): void
        // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
        {
            $user = User::factory()->create();//テストユーザー作成
            
            $this->actingAs($user);//テストユーザーでログイン
            $response = $this->get(route('user.index'));//アドミン一覧ページにアクセス
            // $response->assertRedirect(route('admin.login'));
            $response->assertStatus(200);
        }

        public function test_login_adminuser_cannot_access_to_user_index(): void
        // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
        {
            $user = User::factory()->create();
            $admin = new Admin();
            $admin->email= 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            
            $response = $this->actingAs($admin)->get(route('user.edit',['user' => $user->id]));
            $response->assertStatus(403);
        }



        // // editアクション（会員情報編集ページ）

        public function test_not_login_user_cannot_access_to_user_edit(): void
        // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
        {
            $user = User::factory()->create();
            $response = $this->get('login');//未ログインの状態で'login'にアクセス
            $response = $this->get(route('user.edit',['user' => $user->id]));//アドミン会員詳細ページにアクセス
            $response->assertRedirect(route('login'));
            // $response->assertStatus(403);
        }

        public function test_login_user_cannot_access_to_edit_other_user_info(): void
        // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
        {
            // テストユーザー作成
            $user = User::factory()->create();//テストユーザー作成
            $otherUser = User::factory()->create(); //他のユーザーが編集ページにアクセスするための別ユーザー作成
            $response = $this->actingAs($otherUser)->get(route('user.edit',['user'=>$user->id]));//アドミン詳細ページにアクセス
            $response->assertStatus(403);
        }

        public function test_login_user_can_access_to_edit_own_info(): void
        // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
        {
            $user = User::factory()->create();
            $this->actingAs($user);
            $response = $this->get(route('user.edit',['user' => $user->id]));
            $response->assertStatus(200);
        }

        public function test_login_adminuser_can_access_to_user_edit(): void
        // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
        {
            $admin = Admin::factory()->create([
                'name' => 'Admin Name',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
            $user = User::factory()->create();
            $response = $this->actingAs($admin)->get(route('user.edit',['user' => $user->id, 'otherParameter' => 'value']));
            // $response->assertRedirect(route('login'));
            $response->assertStatus(403);
        
        }
        // // updateアクション（会員情報更新機能）
      
        public function test_not_login_user_cannot_access_to_user_update(): void
        // 未ログインのユーザーは会員情報を更新できない
        {
            $response = $this->get('login');//未ログインの状態で'login'にアクセス
            // $admin = Admin::factory()->create();// テストユーザー作成
            $user = User::factory()->create();
            $response = $this->put(route('user.update',['user' => $user->id]));//会員詳細ページにアクセス
            $response->assertRedirect(route('login'));
            // $response->assertStatus(403);
        }

        public function test_login_user_cannot_access_to_user_update(): void
        // ログイン済みの一般ユーザーは他人の会員情報を更新できない
        {
            // テストユーザー作成
            // $user = User::factory()->create();//テストユーザー作成
            // $otherUser = User::factory()->create([
            //     'name' => 'test2',
            //     'email'=>'test@test.com',
            // ]); //他のユーザーが編集ページにアクセスするための別ユーザー作成
            
            // $this->actingAs($user);//テストユーザーでログイン
            // $response = $this->get(route('user.update',['user'=>$otherUser->id]));//アドミン詳細ページにアクセス

            // // $response->assertRedirect(route('login'));
            // $response->assertStatus(403);
            $user = User::factory()->create();
            $otherUser = User::factory()->create();

            $this->actingAs($user);
            $response = $this->put(route('user.update', ['user' => $otherUser->id]),[
            'name' => 'Updated Name',
            'kana' => 'カタカナ', // 新しく追加
            'email' => 'updated@example.com',
            'password' => 'newpassword', // 新しく追加
            'password_confirmation' => 'newpassword', // パスワード確認
            'postal_code' => '1234567', // 新しく追加
            'address' => 'Updated Address', // 新しく追加
            'phone_number' => '08012345678', // 新しく追加
            ]);
        // $response = $this->actingAs($user)->put('user/update',[
        
        // ]);
        
        // リダイレクト先をアサート
        // $response->assertRedirect('/home');
        $response->assertStatus(403);

        // リダイレクト先が期待通りであることを確認した後、403エラーが返されるか確認
        // $this->assertEquals(403, $response->baseResponse->status());
            }

        public function test_login_user_can_update_own_user_info(): void
        // ログイン済みの一般ユーザーは自身の会員情報を更新できる
        {
            $user = User::factory()->create(); 
            // $this->actingAs($user);
            $response = $this->actingAs($user)->put(route('user.update',$user),[
                // 更新するデータを送信する
                'name' => 'Updated Name',
                'kana' => 'カタカナ', 
                'email' => 'updated@example.com',
                'password' => 'newpassword', 
                'password_confirmation' => 'newpassword', // パスワード確認
                'postal_code' => '1234567', 
                'address' => 'Updated Address', 
                'phone_number' => '08012345678', 
            ]);
            $response->assertStatus(302); // サーバーエラーのステータスコードを確認
        }

        public function test_login_adminuser_cannot_access_to_user_update(): void
        // ログイン済みの管理者は会員情報を更新できない      
        {
            // $admin = new Admin();
            // $admin->email= 'admin@example.com';
            // $admin->password = Hash::make('nagoyameshi');
            $admin = Admin::factory()->create();
            $user = User::factory()->create(); 
            $response = $this->actingAs($admin)->put(route('user.update',$user,[
                    // 更新するデータを送信する
                    'name' => 'Updated Name',
                    'kana' => 'カナ', // 追加
                    'email' => 'updated@example.com',
                    'postal_code' => '1234567', // 追加
                    'address' => 'Updated Address', // 追加
                    'phone_number' => '08012345678', // 追加
            ]));
            // $response->assertRedirect(route('user.index')); // 未認証の場合
            $response->assertStatus(403);
        }
}
