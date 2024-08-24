<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_drop_foreign_keys_from_regular_holiday_restaurant_table.php
    public function up()
    {
        Schema::table('regular_holiday_restaurant', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['regular_holiday_id']);
        });
    }

    public function down()
    {
        Schema::table('regular_holiday_restaurant', function (Blueprint $table) {
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->cascadeOnDelete();
            $table->foreign('regular_holiday_id')->references('id')->on('regular_holidays')->cascadeOnDelete();
        });
    }

};
