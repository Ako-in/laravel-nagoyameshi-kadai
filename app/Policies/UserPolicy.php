<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    use HandlesAuthorization;

    public function update($authUser, User $user)
    {
        if ($authUser instanceof Admin) {
            return false; // 管理者がユーザーの更新を行えないようにする
        }

        // 通常のユーザー権限チェック
        return $authUser->id === $user->id;
    }

    
}
