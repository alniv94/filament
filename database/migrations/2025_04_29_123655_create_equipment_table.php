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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_number')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('model_name')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('date_purchased')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->dateTime('last_maintenance_date')->nullable();
            $table->dateTime('next_maintenance_date')->nullable();
            $table->string('remaining_days_for_maintenance')->nullable();
            $table->decimal('fuel_consumption_number', 15, 2)->nullable();
            $table->decimal('size_number', 15, 2)->nullable();
            $table->decimal('capacity_max', 15, 2)->nullable();
            $table->decimal('capacity_tip', 15, 2)->nullable();
            $table->decimal('acel_rate_dry', 15, 2)->nullable();
            $table->decimal('acel_rate_hour', 15, 2)->nullable();
            $table->decimal('nsjbi_rate_dry', 15, 2)->nullable();
            $table->decimal('nsjbi_rate_hour', 15, 2)->nullable();
            $table->decimal('bare_month', 15, 2)->nullable();
            $table->decimal('per_trip', 15, 2)->nullable();
            $table->string('est_repair_cost', 15, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->dateTime('date_issued')->nullable();
            $table->string('status')->nullable();


            // Eloquent Connection
            // $table->foreignId('size_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('fuel_consumption_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('location_id')->constrained()->cascadeOnDelete();

            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('classification_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
