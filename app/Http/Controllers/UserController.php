<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Authファサードを読み込む
use Illuminate\Validation\Rule; //バリデーションの設定に便利なメソッドを提供してくれるクラス
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Gate;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $user = Auth::user();
    //     return view('user.index',compact('user'));
    // }
    public function index()
{
    // if (auth()->user()->cannot('viewAny', User::class)) {
    //     abort(403, 'Unauthorized');
    // }
    Log::info('444');
    $user = Auth::user();
    Log::info('555');
    return view('user.index', compact('user'));

}



    public function show(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->show($request->only(['name', 'email']));

        return response()->json(['message' => 'User updated successfully'], 200);
    }


    public function edit(User $user)
    {
        // $this->authorize('update', $user); // ポリシーの権限をチェック
        // if (auth()->user()->id !== $user->id) {
        //     abort(403);
        // }
        // return $authUser->id === $user->id;
        // if ($user->id !== Auth::id()) {
        //     return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        // }
        // if(Auth::id()!== $user->id){
        // // 一致しない場合、リダイレクトさせる
        //     return redirect()->route('user.show')->with('error_message', '不正なアクセスです。');
        // }

        if (auth()->user()->id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('user.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,User $user)
    {
        $this->authorize('update', $user);
        // return $authUser->id === $user->id; // 自分自身の情報のみ編集可能
        // $user = User::findOrFail($id);  // $user 変数を定義
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'regex:/\A[ァ-ヴー\s]+\z/u', 'max:255'],
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            // 'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            // 'password' => ['required', 'confirmed', Rule::unique('users')->ignore($user->id)],
            'postal_code' => ['required', 'digits:7'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'digits_between:10, 11'],
            'birthday' => ['nullable', 'digits:8'],
            'occupation' => ['nullable', 'string', 'max:255'],

            'name' => 'required|string|max:255',
            'kana' => 'required|string|regex:/\A[ァ-ヴー\s]+\z/u|max:255',
            'postal_code' => 'required|digits:7',
        'address' => 'required|string|max:255',
        'phone_number' => 'required|digits_between:10,11',
        'birthday' => 'nullable|digits:8',
        'occupation' => 'nullable|string|max:255',
        ]);

        $user->update($request->only([
            'name', 'kana', 'email', 'postal_code', 'address', 'phone_number', 'birthday', 'occupation'
        ]));

        return redirect()->route('user.index')->with('flash_message','会員情報を編集しました。');
    }

}
