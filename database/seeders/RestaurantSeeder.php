<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = new Restaurant();
        $restaurant->name="テスト";
        $restaurant->description = "テスト";
        $restaurant->lowest_price = 1000;
        $restaurant->highest_price = 5000;
        $restaurant->postal_code = "0000000";
        $restaurant->address = "テスト";
        $restaurant->opening_time = "10:00:00";
        $restaurant->closing_time = "20:00:00";
        $restaurant->seating_capacity = 50;
        $restaurant->image = "";
        $restaurant->save();
    }
}
