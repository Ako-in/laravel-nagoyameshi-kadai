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
        Schema::create('regular_holiday_restaurant', function (Blueprint $table) {
            // 主キー
            $table->id();
            // 外部キーとして restaurant_id と regular_holiday_id を定義
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('regular_holiday_id');
            
            // 外部キー
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->cascadeOnDelete();
            $table->foreign('regular_holiday_id')->references('id')->on('regular_holidays')->cascadeOnDelete();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 外部キー制約を削除してからテーブルを削除
        Schema::table('regular_holiday_restaurant', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['regular_holiday_id']);
        });

        // テーブルの削除
        Schema::dropIfExists('regular_holiday_restaurant');
    }
};
