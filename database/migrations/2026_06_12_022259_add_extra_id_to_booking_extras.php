<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_extras', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_extras', 'extra_id')) {
                $table->foreignId('extra_id')->nullable()->after('booking_id')
                    ->constrained('extras')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_extras', function (Blueprint $table) {
            if (Schema::hasColumn('booking_extras', 'extra_id')) {
                $table->dropForeign(['extra_id']);
                $table->dropColumn('extra_id');
            }
        });
    }
};
