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
        Schema::create('agency_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->string('opening_morning_start')->default('08:00');
            $table->string('opening_morning_end')->default('12:00');
            $table->string('opening_afternoon_start')->default('14:00');
            $table->string('opening_afternoon_end')->default('18:00');
            $table->string('working_days')->default('monday,tuesday,wednesday,thursday,friday,saturday');
            $table->integer('minimum_rental_days')->default(1);
            $table->integer('cancellation_hours')->default(24);
            $table->text('cancellation_policy')->nullable();
            $table->text('cancellation_policy_ar')->nullable();
            $table->text('cancellation_policy_fr')->nullable();
            $table->decimal('late_return_fee_per_hour', 10, 2)->default(0);
            $table->boolean('allow_delivery')->default(true);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->boolean('require_deposit')->default(true);
            $table->decimal('default_deposit', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_settings');
    }
};
