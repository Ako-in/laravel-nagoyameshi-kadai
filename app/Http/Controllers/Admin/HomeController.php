<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Reservation;
use Laravel\Cashier\Subscription; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(){
        $total_users = User::count();
        $total_premium_users = Subscription::where('stripe_status', 'active')->count();
        $total_free_users = $total_users - $total_premium_users;
        $total_restaurants = Restaurant::count();
        $total_reservations = Reservation::count();
        $number_of_subscription = Subscription::where('stripe_status', 'active')->count();
        $sales_for_this_month = $number_of_subscription * 300;
        return view('admin.home',compact('total_users','total_premium_users','total_free_users','total_restaurants','total_reservations','sales_for_this_month'));
    }
}
