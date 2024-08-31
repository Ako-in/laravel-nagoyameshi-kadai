<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegularHolidayRestaurant extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regular_holiday_restaurant = new RegularHolidayRestaurant();
        $regular_holiday_restaurant->restaurant_id = 1;
        $regular_holiday_restaurant->regular_holiday_id = 1;
    }
}
