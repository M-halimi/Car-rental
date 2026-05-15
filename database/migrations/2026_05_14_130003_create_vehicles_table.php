<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('registration_number')->unique();
            $table->string('color')->nullable();
            $table->integer('mileage')->default(0);
            $table->enum('transmission', ['manual', 'automatic'])->default('manual');
            $table->enum('fuel_type', ['gasoline', 'diesel', 'electric', 'hybrid'])->default('gasoline');
            $table->integer('seats')->default(5);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('weekly_rate', 10, 2)->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('status', ['available', 'rented', 'maintenance', 'unavailable'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
