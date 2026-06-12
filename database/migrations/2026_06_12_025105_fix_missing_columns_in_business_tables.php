<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix #1: agencies.logo — model fillable references it but column was never created
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'logo')) {
                $table->string('logo')->nullable()->after('description');
            }
        });

        // Fix #2: customers.country, passport_number, license_date — model fillable references them
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'country')) {
                $table->string('country')->nullable()->after('city_id');
            }
            if (! Schema::hasColumn('customers', 'passport_number')) {
                $table->string('passport_number')->nullable()->after('nationality');
            }
            if (! Schema::hasColumn('customers', 'license_date')) {
                $table->date('license_date')->nullable()->after('license_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (Schema::hasColumn('agencies', 'logo')) {
                $table->dropColumn('logo');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            $columns = ['country', 'passport_number', 'license_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
