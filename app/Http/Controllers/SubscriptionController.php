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
//     public function showEditForm()
// {
//     $stripeKey = config('services.stripe.secret');
//     return view('subscription.edit', compact('stripeKey'));
// }
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // log::info('111');
        // 管理者ユーザーであればログインページにリダイレクト
        // if ($request->user()->is_admin) {
        //     // log::info('112');
        //     log::info($request->user());
        //     return redirect()->route('login');
        // }
        // if ($request->user()->subscribed('premium_plan')) {
        //     // log::info('222');
        //     return redirect()->route('subscription.edit'); // 既に加入している場合はリダイレクト
        // }
        // // $admin = User::factory()->create(['is_admin' => true]);
        // // if ($user->is_admin) {
        // //     return redirect()->route('login'); // 管理者ユーザーはリダイレクト
        // // }
        // // log::info('333');
        // $intent = $request->user()->createSetupIntent();
        // return view('subscription.create',compact('intent'));


        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // $user->$request->user();
        $request->user()->newSubscription(
            'premium_plan', 'price_1PzdMARwYcrGBVKOF9TPpaqN'
        )->create($request->paymentMethodId);
        // if(!$request->has('paymentMethodId')){
        //     return redirect()->back()->withErrors(['paymentMethodId' => 'Payment method ID is required.']);
        // }
        // // User::instance(Auth::user()->id);
        // $user = User::find(Auth::user()->id);
        // Route::post('/user/subscribe', function (Request $request) {
        //     $request->user()->newSubscription(
        //         'premium_plan', 'price_monthly'
        //     )->create($request->paymentMethodId);
        // });
        // return redirect()->route('home')->with('flash_message','有料プランへの登録が完了しました。');
        return redirect()->route('subscription.edit')->with('flash_message','有料プランへの登録が完了しました。');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // if ($request->user()->is_admin) {
        //     return redirect()->route('login'); // 管理者はリダイレクト
        // }
        // log::info('aaa');
        if (auth()->user()->is_admin) {
            // log::info('bbb');
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        // log::info('ccc');
        // dd($user);
        // $subsctiption = auth::user()->subscription;
        // $user = Auth::user();
        // $subscription = Subscription::findOrFail($id);
        $intent = $user->createSetupIntent();
        // log::info('ddd');
        // return view('subscription.edit',compact('subscription'));
        return view ('subscription.edit', compact('user','intent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,User $user)
    {
        $user = $request->user();
        // if(!$user->hasStripeId()){
        //     $user->createAsStripeCustomer();
        // }

        
        // デフォルトの支払い方法を更新
        // $user->updateDefaultPaymentMethod($paymentMethodId);
        $user->updateDefaultPaymentMethod($request->paymentMethodId);
        return redirect()->route('home')->with('flash_message','お支払い方法を変更しました。');

    }
    
    public function cancel()
    {

        return view('subscription.cancel');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        // $user = User::find(Auth::user()->id);
        // User::instance(Auth::user()->id);
        $subscription = $user->subscription('premium_plan');
        if (!$user->hasStripeId()) {
            $user->stripeMember();
        }
        $user->subscription('premium_plan')->cancelNow();

        return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
        // if ($subscription->canceled()) {
        //     $subscription->delete();
            
        // }
        // サブスクリプションがキャンセルされていない場合の処理
        // return redirect()->route('user.index')->withErrors(['subscription' => 'Subscription is still active.']);
        // if (!$subscription) {
        //     return redirect()->route('user.index')->withErrors(['subscription' => 'Subscription not found.']);
        // }
        // $subscription->cancelNow();
        // $user->delete();
        
        // return redirect()->route('home',compact('user'))->with('flash_message','有料プランを解約しました。');
    }

    
}
