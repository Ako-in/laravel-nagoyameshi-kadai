<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $keyword = $request->input('keyword');
        If($keyword !== null){
            $restaurants = Restaurant::where('name','like',"%{$keyword}%")->paginate(15);
        
        }else{
            $restaurants = Restaurant::paginate(15);
        }
        $total = $restaurants->total();
        return view('admin.restaurants.index',compact('restaurants','keyword','total'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $regular_holidays = RegularHoliday::all();
        return view('admin.restaurants.create',compact('categories','regular_holidays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //バリデーション設定
        $request->validate([    
           'name' =>'required|string|max:255',
           'description' =>'required',
           'lowest_price' =>'required|integer',
           'highest_price' =>'required|integer',
           'postal_code' =>'required|string',
           'address' =>'required|string',
           'opening_time' =>'required',
           'closing_time' =>'required|date_format:H:i|after:opening_time',
           'seating_capacity' =>'required|between:0,200|integer',
           'category_ids' => 'required|array|max:3',  // カテゴリのバリデーション
        //    'regular_holidays'=>'required',
           'image'=>'image|max:2048',
        ]);

        // フォームの入力内容をテーブルにデータを追加する
        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        $restaurant->image = $request->input('image');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        // dd('111');
        // アップロードされたファイル（name="image"）が存在すれば処理を実行する
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('restaurants', 's3');
            $restaurant->image = basename($image);
        }else{
            $restaurant->image = '';
        }
        // dd('444');
        $restaurant->save();
        // dd('555');


        // $categories = $restaurant->category_id;
        $category_ids = array_filter($request->input('category_ids',[]));
        $restaurant->categories()->sync($category_ids);

        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids',[]));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);
        //店舗登録後のリダイレクト先は店舗一覧ページ
        return redirect()->route('admin.restaurants.index')->with('flash_message','店舗を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $restaurant = Restaurant::find($id);
        $regular_holidays = RegularHoliday::find($id);
        // $restaurants = Restaurant::where('name')->get();
        return view('admin.restaurants.show',compact('restaurant','regular_holidays'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
        $restaurant = Restaurant::where('id',$id)->first();
        // レストランが見つからない場合のエラーハンドリング
        if (!$restaurant) {
            abort(404, 'Restaurant not found');
        }
        
        $categories = Category::all();
        // インスタンスに紐づくcategoriesテーブルのすべてのデータをインスタンスのコレクションとして取得する
        // $categories = $restaurant->categories;
        $category_array = $restaurant->categories->toArray();
        // $restaurants = Restaurant::where('id',$id)->first();
        // // 設定されたカテゴリのIDを配列化する
        $category_ids = $restaurant->categories->pluck('id')->toArray();

        $regular_holidays = RegularHoliday::all();
        $regular_holiday_array = $restaurant->regular_holidays->toArray();
        $regular_holiday_ids = $restaurant->regular_holidays->pluck('id')->toArray();
        // return view('admin.restaurants.edit',compact('restaurants','id'));
        return view('admin.restaurants.edit',compact('restaurant','categories','category_ids','regular_holidays','regular_holiday_ids'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id,Request $request)
    {
        //バリデーション設定
        $request->validate([
            'category_ids' => 'required|array|max:3',  // カテゴリのバリデーション
        ]);
        $restaurant = Restaurant::where('id',$id)->first();
        $restaurant->name = $request->input('name');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

        if($request->hasFile('image')){
            // // 画像を保存してそのパスを取得
            $image = $request->file('image')->store('restaurants', 's3');
            $restaurant->image = basename($image);

        }
        $restaurant->save();

        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);
        
        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids',[]));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);
        //リダイレクトさせる
        return redirect()->route('admin.restaurants.show', ['restaurant' => $id])->with('flash_message', '店舗を編集しました。');
    }

    /*
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return to_route('admin.restaurants.index')->with('flash_message','店舗を削除しました。');
    }
}
