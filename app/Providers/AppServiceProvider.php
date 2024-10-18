<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Laravel\Cashier\Cashier;  // Cashier クラスのインポート
use App\Models\User;  // User モデルのインポート
use Laravel\Cashier\Billable;

use Aws\S3\S3Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::environment(['production'])) {
            URL::forceScheme('https');
        }
        Paginator::useBootstrap();

        // Cashier に User モデルを指定
        // Cashier::useCustomerModel(User::class);
        Cashier::useCustomerModel(User::class);
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }
    
}
