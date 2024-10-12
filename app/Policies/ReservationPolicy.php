<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Reservation;

class ReservationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create(User $user, Restaurant $restaurant)
    {
        // 管理者ユーザーは予約を作成できない場合
        return $user->role !== 'admin';
        return $user->subscribed;  // サブスクライブ済みのユーザーだけが予約できる
    }
    
    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class); // ポリシーによる認可
    }

    

    public function delete(User $user, Reservation $reservation)
    {
        if ($user->id !== $reservation->user_id) {
            // 他のユーザーのレビューを削除しようとした場合は403を返す
            abort(403, 'You are not authorized to delete this reservation.');
        }

        // return true;
        return $user->id === $reservation->user_id; // 自分のレビューのみ削除可能
    }

}
