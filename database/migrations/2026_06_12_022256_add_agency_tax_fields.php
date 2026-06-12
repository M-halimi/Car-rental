<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $columns = [
                'registration_number' => ['string', true],
                'tax_id' => ['string', true],
                'legal_form' => ['string', true],
                'capital' => ['string', true],
            ];

            foreach ($columns as $col => [$type, $nullable]) {
                if (! Schema::hasColumn('agencies', $col)) {
                    $table->$type($col)->nullable()->after('email');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $columns = ['registration_number', 'tax_id', 'legal_form', 'capital'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('agencies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
