<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryRestaurant extends Model
{
    use HasFactory;
    protected $table = 'category_restaurants';
    protected $fillable = [

        'restaurant_id',
        'category_id',
        'created_at',
        'updated_at',
        // 'name',
        // 'description',
        // 'lowest_price',
        // 'highest_price',
        // 'postal_code',
        // 'address',
        // 'opening_time',
        // 'closing_time',
        // 'seating_capacity',
        // 'image',
    ];

    

    



}
