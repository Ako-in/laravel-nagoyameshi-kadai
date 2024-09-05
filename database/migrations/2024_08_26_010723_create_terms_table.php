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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2024_08_22_200605_create_regular_holidays_table.php
        Schema::dropIfExists('regular_holidays');
========

        Schema::dropIfExists('terms');
>>>>>>>> feature-admin-basic-information:database/migrations/2024_08_26_010723_create_terms_table.php

    }
};
