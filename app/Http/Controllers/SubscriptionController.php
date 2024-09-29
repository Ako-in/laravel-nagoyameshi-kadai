<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

class SubscriptionController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.home');
        }
        if ($user->subscribed('premium_plan')) {
            return redirect()->route('subscription.edit');
        }

        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->user()->newSubscription(
            'premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'
        )->create($request->paymentMethodId);
        return redirect()->route('user.index')->with('flash_message','有料プランへの登録が完了しました。');
    }

    public function edit()
    {   
        $user = Auth::user();
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.home');
        }

        if (!$user->subscribed('premium_plan')) {
            return redirect()->route('subscription.create');
        }
        
        // 通常のユーザー向けの処理
        $intent = $user->createSetupIntent();
        
        // $intent = $user->createSetupIntent();
        return view('subscription.edit', compact('user','intent'));
    }


    public function update(Request $request,User $user)
    {
        $user = $request->user();

        $user->updateDefaultPaymentMethod($request->paymentMethodId);
        return to_route('user.index')->with('flash_message','お支払い方法を変更しました。');

    }
    
    public function cancel()
    {
        $user = auth()->user();
        // 管理者ならリダイレクト
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.home');
        }
         // 無料ユーザーの場合はリダイレクト
        if (!$user->subscribed('premium_plan')) {
            return redirect()->route('subscription.create');
        }
        return view('subscription.cancel');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.home');
        }
        $subscription = $user->subscription('premium_plan');
        if (!$user->hasStripeId()) {
            $user->stripeMember();
        }
        $user->subscription('premium_plan')->cancelNow();

        return to_route('home')->with('flash_message', '有料プランを解約しました。');
    }
}
