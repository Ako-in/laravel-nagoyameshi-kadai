<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Cashier;
// use App\Models\Cashier\User;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'kana',
        'email',
        'password',
        'postal_code',
        'address',
        'phone_number',
        'birthday',
        'occupation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
     * アプリケーション全サービスの初期起動処理
     */
    // public function boot(): void
    // {
    //     Cashier::useCustomerModel(User::class);
    // }

    // 静的な boot() メソッド（エラー解消）
    protected static function boot():void
    {
        parent::boot();

        // 追加の boot 処理
        Cashier::useCustomerModel(User::class);

        
    }

    public function isAdmin()
    {
        return $this->is_admin; // 例えば、is_admin フラグが存在する場合
    }

    public function isSubscribed()
    {
        return $this->subscriptions()->exists(); // subscriptions() メソッドが null を返さないか確認
    }
    

    // public function subscription()
    // {
    //     // Subscription モデルとのリレーションを定義 (例)
    //     return $this->hasOne(Subscription::class);
    // }


    public function reviews(){
        return $this->hasMany(Review::class);
    }
}
