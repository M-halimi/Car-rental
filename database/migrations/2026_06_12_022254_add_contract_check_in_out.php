<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            // Check-in (pickup)
            if (! Schema::hasColumn('rental_contracts', 'odometer_pickup')) {
                $table->integer('odometer_pickup')->nullable()->after('signed_at');
            }
            if (! Schema::hasColumn('rental_contracts', 'fuel_level_pickup')) {
                $table->enum('fuel_level_pickup', ['empty', '1/4', '1/2', '3/4', 'full'])->nullable()->after('odometer_pickup');
            }
            if (! Schema::hasColumn('rental_contracts', 'check_in_notes')) {
                $table->text('check_in_notes')->nullable()->after('fuel_level_pickup');
            }
            if (! Schema::hasColumn('rental_contracts', 'check_in_photos')) {
                $table->json('check_in_photos')->nullable()->after('check_in_notes');
            }
            if (! Schema::hasColumn('rental_contracts', 'check_in_damages')) {
                $table->json('check_in_damages')->nullable()->after('check_in_photos');
            }

            // Check-out (return)
            if (! Schema::hasColumn('rental_contracts', 'odometer_return')) {
                $table->integer('odometer_return')->nullable()->after('check_in_damages');
            }
            if (! Schema::hasColumn('rental_contracts', 'fuel_level_return')) {
                $table->enum('fuel_level_return', ['empty', '1/4', '1/2', '3/4', 'full'])->nullable()->after('odometer_return');
            }
            if (! Schema::hasColumn('rental_contracts', 'check_out_notes')) {
                $table->text('check_out_notes')->nullable()->after('fuel_level_return');
            }
            if (! Schema::hasColumn('rental_contracts', 'check_out_photos')) {
                $table->json('check_out_photos')->nullable()->after('check_out_notes');
            }
            if (! Schema::hasColumn('rental_contracts', 'check_out_damages')) {
                $table->json('check_out_damages')->nullable()->after('check_out_photos');
            }

            // Charges
            if (! Schema::hasColumn('rental_contracts', 'damage_charge')) {
                $table->decimal('damage_charge', 10, 2)->default(0)->after('check_out_damages');
            }
            if (! Schema::hasColumn('rental_contracts', 'fuel_charge')) {
                $table->decimal('fuel_charge', 10, 2)->default(0)->after('damage_charge');
            }
            if (! Schema::hasColumn('rental_contracts', 'additional_charges')) {
                $table->decimal('additional_charges', 10, 2)->default(0)->after('fuel_charge');
            }
            if (! Schema::hasColumn('rental_contracts', 'charge_notes')) {
                $table->text('charge_notes')->nullable()->after('additional_charges');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            $columns = [
                'odometer_pickup', 'fuel_level_pickup', 'check_in_notes', 'check_in_photos', 'check_in_damages',
                'odometer_return', 'fuel_level_return', 'check_out_notes', 'check_out_photos', 'check_out_damages',
                'damage_charge', 'fuel_charge', 'additional_charges', 'charge_notes',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('rental_contracts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
