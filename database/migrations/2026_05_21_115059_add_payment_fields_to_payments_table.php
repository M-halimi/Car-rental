<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('amount');
            $table->decimal('remaining_balance', 10, 2)->nullable()->after('deposit_amount');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('remaining_balance');
            $table->timestamp('due_date')->nullable()->after('refunded_amount');
            $table->string('proof_of_payment')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'remaining_balance', 'refunded_amount', 'due_date', 'proof_of_payment']);
        });
    }
};
