<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Live "Call Waiter / Water / Bill" alerts per table
        Schema::create('table_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_element_id')->constrained('floor_elements')->cascadeOnDelete();
            $table->string('request_type')->default('waiter'); // waiter, water, bill
            $table->string('note')->nullable();
            $table->string('status')->default('pending'); // pending, resolved
            $table->unsignedBigInteger('resolved_by_staff_id')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // 2. Customer table orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_element_id')->nullable()->constrained('floor_elements')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('status')->default('pending'); // pending, preparing, served, paid, cancelled
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('handled_by_staff_id')->nullable()->index();
            $table->timestamps();
        });

        // 3. Line items inside each order
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 4. Staff attendance & shift records
        Schema::create('staff_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('staff_id')->index();
            $table->timestamp('clock_in_at');
            $table->timestamp('clock_out_at')->nullable();
            $table->unsignedInteger('orders_served')->default(0);
            $table->unsignedInteger('calls_resolved')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_shifts');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('table_calls');
    }
};
