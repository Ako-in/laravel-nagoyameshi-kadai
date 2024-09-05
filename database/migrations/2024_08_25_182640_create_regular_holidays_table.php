<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
<<<<<<<< HEAD:database/migrations/2024_08_22_201607_create_regular_holiday_restaurant_table.php
        Schema::create('regular_holiday_restaurant', function (Blueprint $table) {
            $table->id();
========
        Schema::create('regular_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->integer('day_index')->nullable();
>>>>>>>> feature-admin-basic-information:database/migrations/2024_08_25_182640_create_regular_holidays_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2024_08_22_201607_create_regular_holiday_restaurant_table.php
        Schema::dropIfExists('regular_holiday_restaurant');
========
        Schema::dropIfExists('regular_holidays');
>>>>>>>> feature-admin-basic-information:database/migrations/2024_08_25_182640_create_regular_holidays_table.php
    }
};
