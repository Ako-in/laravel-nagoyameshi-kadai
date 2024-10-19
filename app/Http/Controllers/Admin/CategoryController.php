<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        If($keyword !== null){
            $categories = Category::where('name','like',"%{$keyword}%")->paginate(15);
        }else{
            $categories = Category::paginate(15);
        }
        $total = $categories->total();
        return view('admin.categories.index',compact('categories','keyword','total'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //バリデーション設定
        $request->validate([
            'name' =>'required',
        ]);
        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        //カテゴリ登録後のリダイレクト先はカテゴリ一覧ページ
        return redirect()->route('admin.categories.index')->with('flash_message','カテゴリを登録しました。');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.categories.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $category = Category::where('id',$id)->first();
        $category->name = $request->input('name');
        $category->save();
        Log::info('カテゴリが更新されました: ', ['category' => $category]);
        //リダイレクトさせる
        return redirect()->route('admin.categories.index', ['category' => $id,'regular_holiday'=> $id])->with('flash_message', 'カテゴリを編集しました。');
    }


    // public function update(Request $request, Category $category) {
    //     $request->validate([
    //         'name' => 'required',
    //     ]);

    //     $category->name = $request->input('name');
    //     $category->save();

    //     return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを編集しました。');
    // }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Category $category)
    {
        $category->delete();
        dd($category);
        return to_route('admin.categories.index')->with('flash_message','カテゴリを削除しました。');
    }
}
