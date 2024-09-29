<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Auth;

class Subscribed
{
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
    $user = Auth::user();

    if (!$request->user()?->subscribed('premium_plan')) {
        // ユーザーがプレミアムプランに加入していない場合にsubscription/createにリダイレクト
        return redirect('subscription.create');
    } 
    return $next($request);
}

}
