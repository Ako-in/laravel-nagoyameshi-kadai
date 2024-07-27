<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;


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
        return view('admin.restaurants.create');
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

        // アップロードされたファイル（name="image"）が存在すれば処理を実行する
        if ($request->hasFile('image')) {
            // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$image_pathに代入する
            $image = $request->file('image')->store('public/restaurants');
            // ファイルパスからファイル名のみを取得し、Productインスタンスのimage_nameプロパティに代入する
            $restaurant->image = basename($image);
        }else{
            $restaurant = new Restaurant();
            $restaurant->image = '';
        }
        $restaurant->save();

        //店舗登録後のリダイレクト先は店舗一覧ページ
        return redirect()->route('admin.restaurants.index')->with('flash_message','店舗を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $restaurant = Restaurant::find($id);
        // $restaurants = Restaurant::where('name')->get();
        return view('admin.restaurants.show',compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
        $restaurants = Restaurant::where('id',$id)->first();
        return view('admin.restaurants.edit',compact('restaurants','id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->input());
        $restaurant = Restaurant::where('id',$id)->first();
        $restaurant->name = $request->input('name');
        // $restaurant->image = empty($request->file('image')) ? '' : $request->file('image');

        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        
        // アップロードされたファイル（name="image"）が存在すれば処理を実行する
        if ($request->hasFile('image')) {
            // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$image_pathに代入する
            $image = $request->file('image')->store('public/restaurants');
            // ファイルパスからファイル名のみを取得し、Productインスタンスのimage_nameプロパティに代入する
            $restaurant->image = basename($image);
        }
            // }else{
        //     $restaurant->image = '';
        // }
        $restaurant->save();

        // // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$image_pathに代入する
        // if(empty($request->file('image'))){
        //     $restaurant->image = '';
        // }else{
        //     $request->file('image')->store('public/restaurants');
        //     $restaurant->image = basename($image_path);
        // }
        // // $image_path = $request->file('image')->store('public/restaurants');
        // // ファイルパスからファイル名のみを取得し、Sampleインスタンスのimage_nameプロパティに代入する
        // // $restaurant->image_name = basename($image_path);
        // $test = $restaurant->save();
        // session()->flash('message','店舗を編集しました。');
        // Session::flash('message','店舗を編集しました。');
        //    return redirect()->route('admin.restaurants.edit',['restaurant'=>$id]);
        //リダイレクトさせる
        return redirect()->route('admin.restaurants.edit', ['restaurant' => $id])->with('flash_message', '店舗を編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return to_route('admin.restaurants.index')->with('flash_message','店舗を削除しました。');
        
    }
}
