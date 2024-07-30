<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;
    protected $filiable = [
        'name',
    ];
    public function restaurants(){
        return $this->belongsTo(Restaurant::class)->withTimestamps();
    }
}
