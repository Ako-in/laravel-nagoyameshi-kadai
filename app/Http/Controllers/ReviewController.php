<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        // $restaurant から reviews を取得
        // $reviews = $restaurant->reviews; // これで関連するレビューが取得できる
        // $reviews = $restaurant->reviews()->paginate(3); // 1ページに3件表示

        // $restaurant = Restaurant::with('reviews')->find($id);
        // $reviews = Review::all();
        $user = Auth::user();
        if($user->subscribed('premium_plan')){
            // $reviews = Restaurant::withAvg('reviews', 'score')->orderBy('reviews_avg_score', 'desc')->take(5)->get();

            $reviews = Review::where('restaurant_id',$restaurant->id)->orderBy('created_at','desc')->paginate(5);
            $high = Review::where('restaurant_id',$restaurant->id)->orderBy('created_at','desc')->paginate(5);
            // $reviews = Review::orderBy('created_at','desc')->paginate(5);
        }else{
            $reviews = Review::where('restaurant_id',$restaurant->id)->orderBy('created_at','desc')->paginate(3);
            //     $reviews = Restaurant::withAvg('reviews', 'score')->orderBy('reviews_avg_score', 'desc')->take(3)->get();

            // $reviews = Review::orderBy('created_at','desc')->paginate(3);
            // $reviews = Review::where('restaurant_id',$restaurant->id)->sortabke()->paginate(3);
        }

        return view('restaurants.reviews.index', compact('restaurant','reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        // レビュー投稿ページ
        $reviews = $restaurant->reviews;

        return view('restaurants.reviews.create',compact('reviews','restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Restaurant $restaurant)
    {
        // 認証されていないユーザーがレビューを作成できないようにする
         $this->authorize('create', Review::class);

        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        //レビュー投稿機能
        //バリデーション設定
        $request->validate([
           'score' =>'required|numeric|between:1,5',
           'content' =>'required',
        ]);

        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = $request->user()->id; // 認証されたユーザーのIDを取得
        $review->save();

         // レビューが正常に保存された後の処理
        return redirect()->route('restaurants.reviews.index', $restaurant->id)
        ->with('flash_message', 'レビューを投稿しました');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        $restaurant = Restaurant::with('reviews')->find($id);
        $review = Review::all();

        if (!$restaurant) {
            return redirect()->route('restaurants/')->with('error', '店舗が見つかりません。');
        }

        return view('restaurants.show', compact('restaurant','review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant, Review $review, User $user)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        $user = auth()->user();
    
        // サブスクリプション未加入の場合はリダイレクト
        if (!$user->subscribed('premium_plan')) {
            return redirect()->route('subscription.create');
        }
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }
        //レビュー編集ページ
        $user = Auth::user();
        $user_id = $user->id;
        if(!$user_id === Auth::user()){
            return redirect()->route('restaurants.reviews.index',['restaurant'=>$restaurant_id])->with('error_message','不正なアクセスです。');
        }else{
            return view('restaurants.reviews.edit',compact('restaurant','review'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        $user = auth()->user();
        // $this->authorize('update', $review);
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }

        if (!$user->isSubscribed()) {
            abort(403, 'You are not subscribed.');
        }
        
        //レビュー更新機能
        $request->validate([
            'score' =>'required|numeric|between:1,5',
            'content' =>'required',
         ]);

        $review->score = $request->input('score');
        $review->content = $request->input('content');

        $review->update($request->only('score', 'content'));
        Log::info($review);
        // $review->update($request->all());
        return redirect()->route('restaurants.reviews.index',$restaurant)->with('flash_message','レビューを編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Review $review)
    {
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        }
        
        //レビュー削除機能
        $review->delete();
        return to_route('restaurants.reviews.index',['restaurant' => $restaurant->id])->with('flash_message','レビューを削除しました。');
    }
}
