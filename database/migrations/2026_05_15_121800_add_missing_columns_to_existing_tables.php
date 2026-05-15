<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'nationality')) {
                $table->string('nationality')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('customers', 'id_document_path')) {
                $table->string('id_document_path')->nullable()->after('license_number');
            }
            if (! Schema::hasColumn('customers', 'license_document_path')) {
                $table->string('license_document_path')->nullable()->after('id_document_path');
            }
            if (! Schema::hasColumn('customers', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('license_document_path');
            }
            if (! Schema::hasColumn('customers', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('is_verified');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('vehicle_categories')->nullOnDelete()->after('agency_id');
            }
            if (! Schema::hasColumn('vehicles', 'plate_number')) {
                $table->string('plate_number')->nullable()->unique()->after('year');
            }
            if (! Schema::hasColumn('vehicles', 'doors')) {
                $table->tinyInteger('doors')->default(4)->after('fuel_type');
            }
            if (! Schema::hasColumn('vehicles', 'price_per_day')) {
                $table->decimal('price_per_day', 10, 2)->nullable()->after('seats');
            }
            if (! Schema::hasColumn('vehicles', 'images')) {
                $table->json('images')->nullable()->after('description');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'price_per_day')) {
                $table->decimal('price_per_day', 10, 2)->nullable()->after('return_date');
            }
            if (! Schema::hasColumn('bookings', 'extras_price')) {
                $table->decimal('extras_price', 10, 2)->default(0)->after('subtotal');
            }
            if (! Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 10, 2)->nullable()->after('extras_price');
            }
            if (! Schema::hasColumn('bookings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->default(0)->after('total_price');
            }
            if (! Schema::hasColumn('bookings', 'deposit_status')) {
                $table->enum('deposit_status', ['pending', 'paid', 'refunded', 'waived'])->default('pending')->after('deposit_amount');
            }
            if (! Schema::hasColumn('bookings', 'pickup_time')) {
                $table->time('pickup_time')->nullable()->after('pickup_date');
            }
            if (! Schema::hasColumn('bookings', 'return_time')) {
                $table->time('return_time')->nullable()->after('return_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'nationality',
                'id_document_path',
                'license_document_path',
                'is_verified',
                'is_blocked',
            ]);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'category_id',
                'plate_number',
                'doors',
                'price_per_day',
                'images',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'price_per_day',
                'extras_price',
                'total_price',
                'deposit_amount',
                'deposit_status',
                'pickup_time',
                'return_time',
            ]);
        });
    }
};
