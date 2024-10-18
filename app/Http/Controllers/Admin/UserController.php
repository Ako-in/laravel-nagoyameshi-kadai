<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // 追加
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request){

        $users = User::query();
        $keyword = $request->input('keyword');
        if(!empty($keyword)){
            $users->where('name','LIKE',"%{$keyword}%")
            ->orWhere('kana','LIKE',"%{$keyword}%");;
        }

        $users = $users->paginate(15);
        $total = $users->total();
        return view('admin.users.index',compact('users','keyword','total'));

    }

    public function show($id){ 
        // $users = User::where('id',$id)->get();
        $user = User::findOrFail($id); // 指定されたIDに基づいてユーザーを取得

        return view('admin.users.show',compact('user'));
    }
}
