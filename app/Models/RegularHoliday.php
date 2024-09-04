<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularHoliday extends Model
{
    use HasFactory;
    protected $table = 'regular_holidays';
    protected $fillable = [
        'day',
        'day_index',
    ];

    public function restaurants(){
        return $this->belongsToMany(Restaurant::class);
    }

}
