<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Auth;

class Subscribed
{
    // protected $routeMiddleware = [
    //     // 既存のミドルウェア
    //     'auth' => \App\Http\Middleware\Authenticate::class,
    //     'subscribed' => \App\Http\Middleware\Subscribed::class, // ここでミドルウェアを登録
    //     'not.subscribed' => \App\Http\Middleware\NotSubscribed::class,
    // ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    /**
     * 受信リクエストの処理
     */

    public function handle(Request $request, Closure $next): Response
{
    // if ($request->user() && $request->user()->subscribed('premium_plan')) {
    //     return redirect('subscription/create');
    // }
    // return $next($request);
    // if (!auth()->check()) {
    //     return redirect('/home');
    // }
    // return $next($request);
    $user = Auth::user();

    if (!$request->user()?->subscribed('premium_plan')) {
        // ユーザーがプレミアムプランに加入していない場合にsubscription/createにリダイレクト
        return redirect('subscription.create');
    } 
    return $next($request);
}


    // public function handle(Request $request, Closure $next): Response
    // {
    //     if ($request->user() && $request->user()?->subscribed('premium_plan')) {
    //         // ユーザーを支払いページへリダイレクトし、サブスクリプションを購入するか尋ねる
    //         // return redirect('subscription/create');
    //         return $next($request);
    //     }
    //     // if (!Auth::check() || !Auth::user()->subscribed('premium_plan')) {
    //     //     return redirect()->route('user.index'); // サブスクしていないユーザーをリダイレクト
    //     // }
    //     return redirect('subscription/create');

    //     // 
        
    // }
}
