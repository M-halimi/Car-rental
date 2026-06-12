<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $columns = [
                'last_maintenance_date' => ['date', true],
                'next_maintenance_date' => ['date', true],
                'maintenance_interval_km' => ['integer', true],
                'maintenance_notes' => ['text', true],
                'insurance_policy_number' => ['string', true],
                'insurance_expiry' => ['date', true],
                'insurance_provider' => ['string', true],
                'technical_control_expiry' => ['date', true],
                'parking_location' => ['string', true],
                'purchase_date' => ['date', true],
                'purchase_price' => ['decimal:12,2', true],
                'current_value' => ['decimal:12,2', true],
            ];

            foreach ($columns as $col => [$type, $nullable]) {
                if (! Schema::hasColumn('vehicles', $col)) {
                    if ($type === 'decimal:12,2') {
                        $table->decimal($col, 12, 2)->nullable()->after('mileage');
                    } elseif ($type === 'text') {
                        $table->text($col)->nullable()->after('mileage');
                    } elseif ($type === 'integer') {
                        $table->integer($col)->nullable()->after('mileage');
                    } elseif ($type === 'date') {
                        $table->date($col)->nullable()->after('mileage');
                    } else {
                        $table->string($col)->nullable()->after('mileage');
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $columns = [
                'last_maintenance_date', 'next_maintenance_date', 'maintenance_interval_km',
                'maintenance_notes', 'insurance_policy_number', 'insurance_expiry',
                'insurance_provider', 'technical_control_expiry', 'parking_location',
                'purchase_date', 'purchase_price', 'current_value',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
