<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Cashier\Billable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class NotSubscribed
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
    // public function handle(Request $request, Closure $next): Response
    // {
    //     if ($request->user() && $request->user()?->Subscribed('premium_plan')) {
    //         // return redirect('subscription/edit');
    //         return $next($request);
    //     }   
    //     return redirect('subscription/create');
    // }

    public function handle(Request $request, Closure $next): Response
{
    // if ($request->user() && $request->user()->subscribed('premium_plan')) {
    //     return redirect('subscription/edit');
    // }
    // return $next($request);
    $user = Auth::user();
    if ($request->user()?->subscribed('premium_plan')) {
        // ユーザーがプレミアムプランに加入している場合にsubscription.editにリダイレクト
        return redirect('subscription.edit');
    } 
    return $next($request);
}

    
}