<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Billable;
    protected $table = 'admins';
    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const UPDATED_AT = null;

    //Adminはたくさんのレストランを持っている
    public function restaurants(){
        return $this->hasMany(Restaurant::class);
    }
}
