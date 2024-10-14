<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Term;

class TermPolicy
{
    /**
     * Create a new policy instance.
     */
    // public function __construct()
    // {
    //     //
    // }
    
    public function viewAny(Admin $admin)
    {
        // 管理者がアクセスできない場合にfalseを返す
        return $admin->role !== 'admin'; // ここでroleの判定を行う
    }
}
