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
        Schema::create('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'id')) {
                $table->id();
            }
            if (!Schema::hasColumn('admins', 'email')) {
                $table->string('email')->unique();
            }
            if (!Schema::hasColumn('admins', 'password')) {
                $table->string('password');
            }
            if (!Schema::hasColumn('admins', 'rememberToken')) {
                $table->rememberToken();
            }
            if (!Schema::hasColumn('admins', 'timestamps')) {
                $table->timestamps();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'id')) {
                $table->dropColumn('id');
            }
            if (Schema::hasColumn('admins', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('admins', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('admins', 'rememberToken')) {
                $table->dropColumn('rememberToken');
            }
            if (Schema::hasColumn('admins', 'timestamps')) {
                $table->dropColumn('timestamps');
            }
        });
        // Schema::dropIfExists('admins');
    }
};
