<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Admin;
use App\Policies\UserPolicy;
use App\Policies\AdminPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    
    
    // protected $policies = [
    //     'App\Post' => 'App\Policies\PostPolicy',
    // ];

    /**
     * Register any authentication / authorization services.
     */
    // public function boot(): void
    // {
    //     $this->registerPolicies();


    //     Gate::define('isAdmin',function($user){

    //        return $user->role == 'administrator';

    //     });

    // }
    public function boot(): void
    {
        $this->registerPolicies();
        // Gate::define('update', [UserPolicy::class, 'update']);
        Gate::define('update', function ($authUser, $user) {
            // Admin であれば権限なし（403）を返す
            if ($authUser instanceof Admin) {
                return false;
            }
        
            return $authUser instanceof User && $authUser->id === $user->id;
        });

        
        
    }
}
