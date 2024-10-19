<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;

class AdminPolicy
{
    /**
     * Create a new policy instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // public function update(Admin $admin, User $user)
    // {
    //     // 管理者がユーザーを更新できるかどうかのロジック
    //     return true; // 例えば常に許可する場合
    // }
    public function update(Admin $admin)
    {
        // 管理者がユーザーを更新できるかどうかのロジック
        return true; // 例えば常に許可する場合
    }

    public function destroy(Admin $admin)
    {
        // 管理者がユーザーを更新できるかどうかのロジック
        return true; // 例えば常に許可する場合
    }
}
