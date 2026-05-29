<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accurate_audit_sync_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accurate_audit_source_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('last_audit_id', 100)->nullable();
            $table->timestamp('last_activity_time')->nullable();
            $table->string('last_hash', 64)->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_audit_sync_states');
    }
};
