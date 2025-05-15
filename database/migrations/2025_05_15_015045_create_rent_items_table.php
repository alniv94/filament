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
        Schema::create('rent_items', function (Blueprint $table) {
            $table->id();
            $table->string('rent_type')->nullable();
            $table->decimal('rate', 15, 2)->nullable();
            $table->decimal('quantity', 15, 2)->nullable();
            $table->decimal('estimated_discount', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->string('status')->default('OPEN');

            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rent_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_items');
    }
};
