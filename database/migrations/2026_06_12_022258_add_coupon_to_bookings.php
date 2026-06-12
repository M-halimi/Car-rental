<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('discount')
                    ->constrained('coupons')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'discount_type')) {
                $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('coupon_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = ['coupon_id', 'discount_type'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
