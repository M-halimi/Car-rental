<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'source')) {
                $table->string('source')->default('web')->after('notes');
            }
            if (! Schema::hasColumn('bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('source');
            }
            if (! Schema::hasColumn('bookings', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable()->after('confirmed_at');
            }
            if (! Schema::hasColumn('bookings', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('picked_up_at');
            }
            if (! Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('returned_at');
            }
            if (! Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            if (! Schema::hasColumn('bookings', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('cancellation_reason')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = [
                'source', 'confirmed_at', 'picked_up_at', 'returned_at',
                'cancelled_at', 'cancellation_reason', 'created_by', 'updated_by',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
