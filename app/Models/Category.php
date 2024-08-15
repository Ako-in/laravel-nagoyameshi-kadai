<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Restaurant;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    // protected $filiable = [
    //     'name',
    // ];
    protected $guarded = ['id'];
    public function restaurants(){
        return $this->belongsToMany(Restaurant::class)->withTimestamps();
    }
}
