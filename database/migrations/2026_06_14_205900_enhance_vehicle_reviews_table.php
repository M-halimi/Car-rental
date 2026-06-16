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
        Schema::table('vehicle_reviews', function (Blueprint $table) {
            $table->tinyInteger('cleanliness_rating')->nullable()->after('rating');
            $table->tinyInteger('service_rating')->nullable()->after('cleanliness_rating');
            $table->tinyInteger('condition_rating')->nullable()->after('service_rating');
            $table->tinyInteger('value_rating')->nullable()->after('condition_rating');
            $table->json('photos')->nullable()->after('comment');
            $table->boolean('is_verified_booking')->default(false)->after('photos');
            $table->integer('helpful_count')->default(0)->after('is_verified_booking');
            $table->text('agency_response')->nullable()->after('helpful_count');
            $table->timestamp('agency_responded_at')->nullable()->after('agency_response');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'cleanliness_rating',
                'service_rating',
                'condition_rating',
                'value_rating',
                'photos',
                'is_verified_booking',
                'helpful_count',
                'agency_response',
                'agency_responded_at',
            ]);
        });
    }
};
