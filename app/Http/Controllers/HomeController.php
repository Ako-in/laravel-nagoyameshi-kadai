<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\CategoryRestaurant;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$highly_rated_restaurantsはレビューの高い順に並べ替え必要！！！
        // $highly_rated_restaurants = Restaurant::orderBy('rating','desc')->take(6)->get();
        $highly_rated_restaurants = Restaurant::withAvg('reviews', 'score')->orderBy('reviews_avg_score', 'desc')->take(6)->get();
        $categories = Category::all(); 
        //$new_restaurantsから最新の6件を取得する
        $new_restaurants = Restaurant::orderBy('created_at','desc')->take(6)->get();
        return view('home',compact('highly_rated_restaurants','categories','new_restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Restaurant $restaurant, Category $category)
    // {
    //     // $categories = CategoryRestaurant::all();
    //     $categories = Category::with('cateogries')->find($id);
    //     return view('restaurants/show',compact('categories'));
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
