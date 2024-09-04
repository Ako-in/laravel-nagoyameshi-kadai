<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

class TermTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（利用規約ページ）
    // 未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_not_login_adminuser_cannot_access_to_term(): void
    {
        $response = $this->get('login');
        $response = $this->get('admin/terms/index');
        $response->assertRedirect(route('admin.login'));
    }
    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_login_user_cannot_access_to_term(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->actingAs($user)->get(route('admin.terms.index'));
        $term = Term::factory()->make();

        $response = $this->get(route('admin.terms.index',$term));
        $response->assertRedirect(route('admin.login'));
    }
    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_login_adminuser_can_access_to_term():void
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $term = Term::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.index', ['term' => $term->id]));
        $response->assertStatus(200);
        // $response->assertOk();

    }

    // editアクション（利用規約編集ページ）
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_not_login_adminuser_cannot_edit_term(): void
    {
        $term = Term::factory()->create();
        $response = $this->get(route('admin.terms.edit',$term));

        $response->assertRedirect(route('admin.login'));
    }
    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_login_user_cannot_edit_term(): void
    {
        $user = User::factory()->create();

        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $term = Term::factory()->create();//テストカンパニー作成
        $response = $this->actingAs($user)->get(route('admin.terms.edit', $term));
        // $response->assertStatus(200);
        $response->assertRedirect(route('admin.login'));

    }
    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_login_adminuser_can_edit_term():void
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $term = Term::factory()->create();
        $response = $this->actingAs($admin, 'admin')->put(route('admin.terms.update', $term), [
            'content' => 'テスト',
        ]);
        $response->assertRedirect(route('admin.terms.index')); // 成功後、インデックスにリダイレクトされることを確認
    }
    // updateアクション（利用規約更新機能）
    // 未ログインのユーザーは利用規約を更新できない
    public function test_not_login_adminuser_cannot_update_term(): void
    {
        // $user = User::factory()->create();
        // $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $old_term = Term::factory()->create();
        $new_term = [
            'content'=>'テスト',
        ];
        $response = $this->put(route('admin.terms.update', $old_term), $new_term);
        $this->assertDatabaseHas('terms', ['content' => 'テスト']);
        // $this->assertDatabaseMissing('terms',$new_term);
        $response->assertRedirect(route('admin.login'));

    }
    // ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_login_user_ccannot_update_term(): void
    {
        $user = User::factory()->create();
        $old_term = Term::factory()->create();
        $new_term = [
            'content'=>'テスト',
        ];
        $response = $this->actingAs($user)->put(route('admin.terms.update',$old_term),$new_term);
        // $this->assertDatabaseMissing('company', $new_restaurant);
        $response->assertRedirect(route('admin.login'));

    }
    // ログイン済みの管理者は利用規約を更新できる
    public function test_login_adminuser_can_update_term():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_term = Term::factory()->create();
        $new_term = [
            'content'=>'テスト',
        ];
        $response = $this->actingAs($admin, 'admin')->put(route('admin.terms.update', $old_term), $new_term);
        $this->assertDatabaseHas('terms',$new_term);
        $response->assertRedirect(route('admin.terms.index'));
    }
}
