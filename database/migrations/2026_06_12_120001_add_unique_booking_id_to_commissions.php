<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_commissions', function (Blueprint $table) {
            $table->unique('booking_id', 'booking_commissions_booking_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('booking_commissions', function (Blueprint $table) {
            $table->dropUnique('booking_commissions_booking_id_unique');
        });
    }
};
