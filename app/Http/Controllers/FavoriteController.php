<?php

namespace App\Http\Controllers;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Review;
use App\Models\Reservation;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 管理者ユーザーは管理者ホームにリダイレクト
        if ($user->is_admin) {
            return redirect()->route('admin.home');
        }
        // ユーザーが有料プランに加入していない場合、サブスクリプション作成ページにリダイレクト
        if (!$user->subscribed('premium_plan')) {
            return redirect()->route('subscription.create');
        }

        // if($user->subscribed('premium_plan')){
            // お気に入りの店舗を追加
            $favorite_restaurants = $user->favorite_restaurants()
            ->orderBy('restaurant_user.created_at', 'desc')->paginate(15);
        // }
        return view('favorites.index', compact('favorite_restaurants'));
    }

    public function store($restaurant_id,Request $request)
    {
        $user = Auth::user(); // 現在ログイン中のユーザーを取得
        
        // 管理者ユーザーは管理者ホームにリダイレクト
        if ($user->is_admin) {
            return redirect()->route('admin.login');
        }
        // ユーザーが有料プランに加入していない場合、サブスクリプション作成ページにリダイレクト
        if (!$user->subscribed('premium_plan')) {
            return redirect()->route('subscription.create');
        }

        if ($user->subscribed('premium_plan')) {
            // 中間テーブルにデータを追加
            $user->favorite_restaurants()->attach($restaurant_id);
            return redirect()->back()->with('flash_message', '店舗をお気に入りに追加しました！');
            // return redirect()->route('favorites.index')->with('flash_message', '店舗をお気に入りに追加しました！');
        }
            // return redirect()->route('favorites.index');
        
    }

    
    public function destroy($restaurant_id)
    {
        $user = Auth::user(); // 現在ログイン中のユーザーを取得
        if ($user->is_admin) {
            return redirect()->route('admin.login');
        }
        if ($user->subscribed('premium_plan')) {
            
            $user->favorite_restaurants()->detach($restaurant_id);

            return redirect()->back()->with('flash_message', 'お気に入りを削除しました！');
            // return redirect()->route('favorites.index')->with('flash_message', 'お気に入りを削除しました！');
        }
        
        // return redirect()->back()->with('flash_message', 'お気に入り解除に失敗しました。');
    }
}
