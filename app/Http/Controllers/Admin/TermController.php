<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $terms= Term::get()->last();
        return view('admin.terms.index',compact('terms'));
    }

    public function edit(Term $term)
    {
        return view('admin.terms.edit',compact('term'));
    }

    public function update(Request $request, string $id)
    {
        //バリデーション設定
        $request->validate([
            'content' =>'required',
        ]);
        $term = new Term();
        $term->content = $request->input('content');
        $term->save();
        return redirect()->route('admin.terms.index')->with('flash_message','利用規約を編集しました。');
    }

    
}
