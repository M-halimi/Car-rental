<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->rebuildFk('bookings', 'vehicle_id', 'vehicles');
        $this->rebuildFk('bookings', 'customer_id', 'customers');
        $this->rebuildFk('payments', 'booking_id', 'bookings');
        $this->rebuildFk('rental_contracts', 'booking_id', 'bookings');
        $this->rebuildFk('booking_commissions', 'booking_id', 'bookings');
        $this->rebuildFk('booking_commissions', 'agency_id', 'agencies');
        $this->rebuildFk('platform_commission_payments', 'agency_id', 'agencies');
        $this->rebuildFk('commission_payment_items', 'platform_commission_payment_id', 'platform_commission_payments');
        $this->rebuildFk('commission_payment_items', 'booking_commission_id', 'booking_commissions');
    }

    public function down(): void
    {
        $this->rebuildFk('bookings', 'vehicle_id', 'vehicles', 'CASCADE');
        $this->rebuildFk('bookings', 'customer_id', 'customers', 'CASCADE');
        $this->rebuildFk('payments', 'booking_id', 'bookings', 'CASCADE');
        $this->rebuildFk('rental_contracts', 'booking_id', 'bookings', 'CASCADE');
        $this->rebuildFk('booking_commissions', 'booking_id', 'bookings', 'CASCADE');
        $this->rebuildFk('booking_commissions', 'agency_id', 'agencies', 'CASCADE');
        $this->rebuildFk('platform_commission_payments', 'agency_id', 'agencies', 'CASCADE');
        $this->rebuildFk('commission_payment_items', 'platform_commission_payment_id', 'platform_commission_payments', 'CASCADE');
        $this->rebuildFk('commission_payment_items', 'booking_commission_id', 'booking_commissions', 'CASCADE');
    }

    private function rebuildFk(string $table, string $column, string $ref, string $onDelete = 'RESTRICT'): void
    {
        $fkName = "{$table}_{$column}_foreign";

        $exists = DB::select(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $fkName, 'FOREIGN KEY']
        );

        if (! empty($exists)) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`");
        }

        DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$column}`) REFERENCES `{$ref}` (`id`) ON DELETE {$onDelete}");
    }
};
