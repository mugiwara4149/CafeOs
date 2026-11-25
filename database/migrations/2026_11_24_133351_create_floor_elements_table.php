<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floor_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('element_type', 20)->default('table'); // table, wall, plant, counter
            $table->string('label', 50)->nullable();              // e.g. "Table 01"
            $table->string('shape', 20)->default('rect');         // rect, circle
            $table->integer('x')->default(40);
            $table->integer('y')->default(40);
            $table->integer('width')->default(90);
            $table->integer('height')->default(90);
            $table->integer('rotation')->default(0);
            $table->integer('seats')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floor_elements');
    }
};