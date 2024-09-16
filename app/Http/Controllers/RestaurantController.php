<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // if (Auth::guard('admin')->check()) {
        //     abort(403, 'This action is unauthorized.');
        // }
        // $user = $request->user(); // 1人のユーザーを取得
        // $admin = $request->admin();
        // if ($user !==$admin()) {
        //     return redirect()->route('admin.home');
        // }

        $user = $request->user(); // 現在のユーザーを取得
    if ($user && $user->is_admin) {
        // 管理者なら別の処理を行う
        return redirect('/admin/home');
    }


        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $price = $request->input('price');

        $categories = Category::all();

        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc'
        ];
        $sort_query = [];
        $sorted = "created_at desc";

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1]->sortable('orderBy')->paginate(15);
            $sorted = $request->input('select_sort');
        }

        $keyword = $request->input('keyword');
        If($keyword !== null){
            $restaurants = Restaurant::whereHas('categories',function($query)use($keyword){
                $query->where('categories.name','like',"%{$keyword}%");
            })
            ->orWhere('address', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            // ->orWhereHas('restaurants',function($query)use($keyword){
            //     $query->where('restaurants.address','like',"%{$keyword}%");
            // })
            // ->orWhereHas('restaurants',function($query)use($keyword){
            //     $query->where('restaurants.name','like',"%{$keyword}%");
            // })
            ->sortable($sort_query)
            ->orderBy('created_at','desc')
            ->paginate(15);

            $total = $restaurants->total();
            
        }elseif($category_id !== null){

            $restaurants = Restaurant::whereHas('categories', function($query) use ($category_id) {
                $query->where('categories.id',$category_id);
            })->sortable($sort_query)
              ->orderBy('created_at', 'desc')
              ->paginate(15);
    
            $total = $restaurants->total(); // paginate() から total を取得

        }elseif($price !== null){
            $restaurants = Restaurant::where('lowest_price','<=',$price)
            ->sortable($sort_query)
            ->orderBy('created_at','desc')
            ->paginate(15);
            $total = $restaurants->total();
        }else{
            $restaurants = Restaurant::sortable($sort_query)
            ->orderBy('created_at','desc')
            ->paginate(15);
            $total = $restaurants->total();

        }
        return view('restaurants.index',compact('restaurants','keyword','total','category_id','price','sorts','sorted','sort_query','categories'));
    }


    public function show(Request $request, $id){
        if (auth()->guard('admin')->check()) {
            abort(403); // 403 Forbidden
        }
        $restaurant = Restaurant::find($id);
        return view('restaurants.show',compact('restaurant'));
    }

}
