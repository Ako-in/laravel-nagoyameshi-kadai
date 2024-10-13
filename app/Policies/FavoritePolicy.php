<?php

namespace App\Policies;

use App\Models\User;

class FavoritePolicy
{
    /**
     * Create a new policy instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    public function store(Request $request, Restaurant $restaurant)
    {
        return $user->isSubscribed(); // ユーザーがサブスクリプション中であれば true
    }
}
