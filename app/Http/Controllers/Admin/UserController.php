<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $keyword = $request->input('keyword');
        $users = User::paginate(15);
        if(!empty($keyword)){
            $query->where('name','LIKE',"%{$keyword}%")
                  ->orWhere('kana','LIKE',"%{$keyword}%");
        }
        $total =
        return view('users.index',compact('users','keyword'));
    }
}
