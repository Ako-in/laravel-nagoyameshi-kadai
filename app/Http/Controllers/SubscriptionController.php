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
    // public function __construct()
    // {
    //     $this->middleware('auth');// ログインしているかを確認
    //     $this->middleware('verified'); // メール確認済みかを確認
    // }

    
    public function create()
    {
        Log::info('aaa1');
        var_dump('aaa2');//returnしたら消える
        dd('aaa3');
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        Log::info('bbb1');
        var_dump('bbb2');//returnしたら消える
        dd('bbb3');
        $user = Auth::user();
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.home');
        }
        if ($user->subscribed('premium_plan')) {
            return redirect()->route('subscription.edit');
        }
        Log::info('ccc1');
        var_dump('ccc2');//returnしたら消える
        dd('ccc3');
        $intent = Auth::user()->createSetupIntent();
        Log::info('ddd1');
        var_dump('ddd2');//returnしたら消える
        dd('ddd3');
        return view('subscription.create', compact('intent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('111-1');
        var_dump('111-2');//returnしたら消える
        dd('111-3');
        $stripeKey = config('services.stripe.secret');
        Log::info('Stripe Secret: ' . $stripeKey); // ログに出力して確認

        // $user = Auth::user();

        // $request->user()->newSubscription(
        //     'premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'
        // )->create($request->paymentMethodId);
        // return redirect()->route('user.index')->with('flash_message','有料プランへの登録が完了しました。');
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        Log::info('222-1');
        var_dump('222-2');//returnしたら消える
        dd('222-3');
        try {
            $request->user()->newSubscription(
                'premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'
            )->create($request->paymentMethodId);
            Log::info('333-1');
            var_dump('333-2');//returnしたら消える
            dd('333-3');//そのあとは止まる
            return redirect()->route('user.index')->with('flash_message','有料プランへの登録が完了しました。');
            

        } catch (\Exception $e) {
            var_dump('444-1');//returnしたら消える
            dd('444-2');//そのあとは止まる
            Log::info('444-3');
            Log::error('Subscription creation failed: '.$e->getMessage());
            return back()->with('error', 'サブスクリプションの登録に失敗しました。');
        }
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

        return to_route('user.index')->with('flash_message', '有料プランを解約しました。');
    }
}
