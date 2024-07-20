<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        // 既存の管理者アカウントを確認して存在しない場合のみ作成
        // $admin = Admin::firstOrCreate(
        //     ['email' => 'admin@example.com'],
        //     ['password' => Hash::make('nagoyameshi')]
        // );

        // 課題レビュー用の管理者アカウントを確認して存在しない場合のみ作成
        // $admin2 = Admin::firstOrCreate([
        //     'name' => 'Admin2',
        //     'email' => 'admin2@example.com',
        //     'password' => Hash::make('admin2_password'),
        // ]);
    }
    
}

