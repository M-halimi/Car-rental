<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('rental', 'deposit', 'extra', 'refund', 'full') NOT NULL DEFAULT 'rental'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('rental', 'deposit', 'extra', 'refund') NOT NULL DEFAULT 'rental'");
    }
};
