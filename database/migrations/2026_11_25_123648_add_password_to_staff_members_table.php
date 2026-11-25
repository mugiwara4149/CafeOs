<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->string('phone', 20)->unique()->nullable()->change();
            $table->string('password')->nullable()->after('phone');
            $table->rememberToken()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->dropRememberToken();
            $table->dropColumn('password');
        });
    }
};
