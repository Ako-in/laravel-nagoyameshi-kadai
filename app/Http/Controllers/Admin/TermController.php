<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $terms= Term::latest()->first();
        return view('admin.terms.index',compact('terms'));
    }

    public function edit(Term $term, $id)
    {
        $terms = Term::get()->last();
        return view('admin.terms.edit',compact('terms'));
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
