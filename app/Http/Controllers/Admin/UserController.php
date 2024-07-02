<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;



class UserController extends Controller
{
    public function index(Request $request){
        $keyword = $request->input('keyword');
        // $total = DB::table('users')->get();
        
        // $users = User::paginate(15);
        
        if($request->user !== null){
            $users = User::where('name','LIKE',"%{$keyword}%")->paginate(15);
        }elseif($keyword !== null ){
            $users = User::where('kana','LIKE',"%{$keyword}%")->paginate(15);
        }else{
            $users = User::paginate(15);
        }
       
        return view('admin.users.index',compact('admin.users.index','keyword'));
    }

    public function store(Request $request){
        $user = new User();
    }
}
