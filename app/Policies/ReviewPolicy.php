<?php

namespace App\Policies;

use App\Models\User;

class ReviewPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create(User $user)
    {
        return $user->isSubscribed(); // ユーザーがサブスクリプション中であれば true
    }


    public function update(User $user, Review $review)
    {
        // サブスクライブ済みか、もしくはレビューの所有者であれば編集を許可
        return $user->isSubscribed() || $user->id === $review->user_id;
        // if ($user->id !== $review->user_id) {
        //     // 他のユーザーのレビューを削除しようとした場合は403を返す
        //     abort(403, 'You are not authorized to delete this review.');
        // }

        return $user->is_subscribed && $user->id === $review->user_id; // ユーザーが自身のレビューを更新できるか確認
    }

    public function delete(User $user, Review $review)
    {
        if ($user->id !== $review->user_id) {
            // 他のユーザーのレビューを削除しようとした場合は403を返す
            abort(403, 'You are not authorized to delete this review.');
        }

        // return true;
        return $user->id === $review->user_id; // 自分のレビューのみ削除可能
    }

}


