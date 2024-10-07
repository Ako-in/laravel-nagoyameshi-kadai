<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Review extends Model
{
    use HasFactory,Sortable;
    protected $fillable = [
        'score', 'content', 'restaurant_id', 'user_id'
    ];
    public function restaurants(){
        return $this->belongsToMany(Restaurant::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
