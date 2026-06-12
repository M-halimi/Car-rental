<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'agencies',
        'cities',
        'vehicle_categories',
        'vehicles',
        'customers',
        'bookings',
        'payments',
        'payment_logs',
        'rental_contracts',
        'booking_extras',
        'vehicle_reviews',
        'agency_settings',
        'booking_commissions',
        'platform_commission_payments',
        'commission_payment_items',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, fn ($t) => $t->softDeletes());
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, fn ($t) => $t->dropSoftDeletes());
            }
        }
    }
};
