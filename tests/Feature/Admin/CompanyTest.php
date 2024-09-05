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

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会社概要ページ）

    public function test_not_login_adminuser_cannot_access_to_company_index(): void
     // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    {
        $response = $this->get('login');
        $response = $this->get('admin/company/index');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_access_to_restaurants_index(): void
    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->actingAs($user)->post(route('admin.company.index'));
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.index',$company));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_access_to_company_index(): void
    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        // $response = $this->actingAs($admin,'admin.company.index')->get('admin.company.index');
        $company = Company::factory()->create();
        // $response = $this->actingAs($admin,'admin')->get('admin/company/index');
        // // $this->assertTrue(Auth::guard('admin')->check());
        // // $response->assertStatus(200);
        // $response->assertOk();
        $response = $this->actingAs($admin,'admin')->get(route('admin.company.index',['company' => $company->id]));

        $response->assertStatus(200);
        // $response->assertOk();
    }


    // editアクション（会社概要編集ページ）
    public function test_not_login_adminuser_cannot_edit_company_profile(): void
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
   {
        $company = Company::factory()->create();
        $response = $this->get(route('admin.company.edit',$company));
        $response->assertRedirect(route('admin.login'));
   }

   public function test_login_user_cannot_edit_company_profile(): void
   // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
   {
      // admin用のユーザーを作成
      $admin = new Admin();
      $admin->email= 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();
      $response = $this->get('login');//未ログインの状態で'login'にアクセス
      $company = Company::factory()->create();//テストカンパニー作成
      $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company->id));
      $response->assertStatus(200);
   }

   public function test_login_adminuser_can_edit_company_profile(): void
   // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
   {
        $admin = new Admin();
        $admin->email= 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $company = Company::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company->id));
        $response->assertStatus(200);
   }

    // updateアクション（会社概要更新機能）

    public function test_not_login_adminuser_cannot_update_to_company_profile(): void
     // 未ログインのユーザーは管理者側の会社概要を更新できない
    {
        $user = User::factory()->create();
        $response = $this->get('login');//未ログインの状態で'login'にアクセス
        $old_company = Company::factory()->create();
        $new_company = Company::factory()->make();
        
        $data = $new_company->toArray();

        $response = $this->patch(route('admin.company.update', $old_company), $data);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_user_cannot_update_to_company_profile(): void
    // ログイン済みの一般ユーザーは管理者側の会社概要を更新できない
    {
        $user = User::factory()->create();
        $old_company = Company::factory()->create();
        $new_company = [
            'name'=>'テスト',
            'postal_code'=>'00000000',
            'address'=>'テスト',
            'representative'=>'テスト',
            'establishment_date'=>'テスト',
            'capital'=>'テスト',
            'business'=>'テスト',
            'number_of_employees'=>'テスト',
        ];
        $response = $this->actingAs($user)->patch(route('admin.company.update',$old_company),$new_company);
        $this->assertDatabaseMissing('companies', $new_company);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_adminuser_can_update_to_company_profile(): void
    // ログイン済みの管理者は管理者側の会社概要を更新できる
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_company = Company::factory()->create();
        $new_company = Company::factory()->make();
        $data = $new_company->toArray();
        $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $old_company), $data);

        $response->assertRedirect(route('admin.company.index'));
    }
}
