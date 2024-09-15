<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;

class RestaurantPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        return !$user->isAdmin(); // 管理者でなければアクセス許可
    }

    

}
