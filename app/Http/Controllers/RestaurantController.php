<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // 現在のユーザーを取得
        if ($user && $user->is_admin) {
            // 管理者なら別の処理を行う
            return redirect('/admin/home');
        }

        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $price = $request->input('price');
        $score = $request->input('score');

        $categories = Category::all();

        // レストランを初期化
        $restaurants = Restaurant::query();


        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
            '予約数が多い順' => 'popular desc'
        ];
        
        $sort_query = [];
        $sorted = "created_at desc"; //デフォルトのソート

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        // デフォルトで $total を0に設定
        $total = 0;

        $keyword = $request->input('keyword');
        if($keyword !== null){
            $restaurants = Restaurant::whereHas('categories',function($query)use($keyword){
                $query->where('categories.name','like',"%{$keyword}%");
            })
            ->orWhere('address', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            ->sortable($sort_query)
            ->orderBy('created_at','desc')
            ->paginate(15);

            $total = $restaurants->total();
            
        }elseif($category_id !== null){

            $restaurants = Restaurant::whereHas('categories', function($query) use ($category_id) {
                $query->where('categories.id',$category_id);
            })
            // ->sortable($sort_query)
            // ->orderBy('created_at','desc')
            // ->orderBy('reviews_count', 'desc')
            ->paginate(15);
    
            $total = $restaurants->total(); // paginate() から total を取得

        }elseif($price !== null){
            $restaurants = Restaurant::where('lowest_price','<=',$price)
            ->sortable($sort_query)
            ->orderBy('created_at','desc')
            ->paginate(15);
            $total = $restaurants->total();

        }else{
            $restaurants = Restaurant::sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
            // $restaurants = Restaurant::orderByRaw($sorted,'desc')->paginate(15);
            $total = $restaurants->total();
        }
        return view('restaurants.index',compact('restaurants','keyword','total','category_id','price','sorts','sorted','sort_query','categories'));
    }


    public function show(Request $request, $id, Review $review){
        if (auth()->guard('admin')->check()) {
            // abort(403); // 403 Forbidden
            return redirect()->route('admin.login');
        }
        $restaurant = Restaurant::with('reviews')->find($id);
        // レストランが存在しない場合の処理
        if (!$restaurant) {
            return redirect()->route('restaurants.index')->with('error', '店舗が見つかりません。');
        }
        return view('restaurants.show',compact('restaurant','review'));
    }

}
