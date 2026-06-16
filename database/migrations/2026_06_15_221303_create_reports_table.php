<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable');
            $table->foreignId('reported_by')->constrained('users');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
