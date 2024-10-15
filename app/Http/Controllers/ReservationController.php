<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant, Reservation $reservation)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        $user = Auth::user();
        // if($user->subscribed('premium_plan')){
        //     // $reservations = Reservation::where('reservation_date',$reservation->id)->orderBy('created_at','desc')->paginate(15);
        //     $reservations = Reservation::where('reservation_date', now())
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(15);
        // }
        if ($user->subscribed('premium_plan')) {
            // $reservations = Reservation::where('user_id', $user->id)
            // // ->where('reservation_time')
            // ->orderBy('reserved_datetime', 'desc')
            // ->paginate(15);
            $reservations = Reservation::where('user_id', Auth::id())
            ->orderBy('reserved_datetime', 'desc')
            ->take(15)
            ->get();
        }
        //  else {
        //     // 必要に応じて他の条件を設定
        //     $reservations = Reservation::where('user_id', $user->id)
        //         ->orderBy('created_at', 'desc')
        //         ->paginate(15);
        // }
        $reservations = Reservation::where('user_id', Auth::id())
        ->orderBy('reserved_datetime', 'desc')
        ->paginate(15);

        // 全てのレストラン情報を取得
        // $restaurants = Restaurant::all();
        // ログインユーザーの予約を取得
        // $reservations = $request->user()->reservations; // もしユーザーに関連付けられた予約があるなら
        // $reservations = $request->user()->reservations ?? []; // 予約がない場合は空の配列を返す

        return view('restaurants.reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        // if (!$user->is_subscribed) {
        //     return redirect()->route('subscription.create');
        // }
        // レビュー投稿ページ
        $reservations = $restaurant->reservations;
        return view('restaurants.reservations.create',compact('reservations','restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Restaurant $restaurant)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        if(!Auth::user()->subscribed('premium_plan')){
        // if (!auth()->user()->subscribed) {
            return redirect()->route('subscription.create');
        } 

        //予約機能
        //バリデーション設定
        $request->validate([
           'reservation_date' =>'required|date_format:Y-m-d',
           'reservation_time' =>'required|date_format:H:i',
           'number_of_people' =>'required|numeric|between:1,50',
        ]);

        $reservation = new Reservation();
        $reservation->reserved_datetime = $request->reservation_date . ' ' . $request->reservation_time;
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $request->user()->id; // 認証されたユーザーのIDを取得
        $reservation->save();

        // レビューが正常に保存された後の処理
        return redirect()->route('reservation.index')
        ->with('flash_message', '予約が完了しました。');

    }


    public function destroy(Restaurant $restaurant, $reservationId)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }

        if(!Auth::user()->subscribed('premium_plan')){
            return to_route('reservation.index',['restaurant' => $restaurant->id])->with('error_message','不正なアクセスです。');
        }
        // 予約削除機能: IDに基づいて予約を取得
        $reservation = Reservation::find($reservationId);
        // ->where('restaurant_id', $restaurant->id)
        // ->firstOrFail();
        // $reservation = Reservation::where('id', $reservationId)->where('restaurant_id', $restaurant->id)->first();
        //予約削除機能
        // Log::info($reservation);
        $reservation->delete();
        // $response->assertRedirect(route('reservations.index'));
        return to_route('reservation.index')
        ->with('flash_message','予約をキャンセルしました。');
    }
}
