<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'status')) {
                $table->string('status', 20)->default('active')->after('is_active');
            }

            if (! Schema::hasColumn('agencies', 'subscription_plan')) {
                $table->string('subscription_plan', 50)->nullable()->after('status');
            }

            if (! Schema::hasColumn('agencies', 'subscription_start_date')) {
                $table->date('subscription_start_date')->nullable()->after('subscription_plan');
            }

            if (! Schema::hasColumn('agencies', 'subscription_end_date')) {
                $table->date('subscription_end_date')->nullable()->after('subscription_start_date');
            }

            if (! Schema::hasColumn('agencies', 'country')) {
                $table->string('country', 100)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'subscription_plan',
                'subscription_start_date',
                'subscription_end_date',
                'country',
            ]);
        });
    }
};
