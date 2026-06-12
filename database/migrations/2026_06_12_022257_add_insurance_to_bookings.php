<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'insurance_package_id')) {
                $table->foreignId('insurance_package_id')->nullable()->after('return_city_id')
                    ->constrained('insurance_packages')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'insurance_fee')) {
                $table->decimal('insurance_fee', 10, 2)->default(0)->after('insurance_package_id');
            }
            if (! Schema::hasColumn('bookings', 'insurance_tax')) {
                $table->decimal('insurance_tax', 10, 2)->default(0)->after('insurance_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = ['insurance_package_id', 'insurance_fee', 'insurance_tax'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
