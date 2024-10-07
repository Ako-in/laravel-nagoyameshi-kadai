<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use DateTimeInterface;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use SerializeDate;
    use HasFactory, Sortable;
    protected $table = 'restaurants';
    protected $fillable = [
        'name',
        'description',
        'lowest_price',
        'highest_price',
        'postal_code',
        'address',
        'opening_time',
        'closing_time',
        'seating_capacity',
        'image',
        'day',
        'day_index',
        'rating',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',    // ←日付の形式を指定
        'updated_at' => 'datetime:Y-m-d H:i:s',    // ←日付の形式を指定
    ];
    public function categories(){
        // return $this->belongsToMany(Category::class)->withTimestamps();
        // belongsToMany('多対多の相手側のクラス名…ClassName::class',
        // '中間テーブルの名前','中間テーブル外部キー名', '中間テーブル外部キー名')
        return $this->belongsToMany(Category::class,'category_restaurants');
    }

    public function regular_holidays(){
        return $this->belongsToMany(RegularHoliday::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

}
