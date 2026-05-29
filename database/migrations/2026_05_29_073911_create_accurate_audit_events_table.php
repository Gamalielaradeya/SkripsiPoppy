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
        Schema::create('accurate_audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accurate_audit_source_id')->constrained()->cascadeOnDelete();
            $table->string('accurate_audit_id', 100);
            $table->string('accurate_user_id', 100)->nullable();
            $table->timestamp('activity_time');
            $table->string('accurate_username', 150)->nullable();
            $table->string('accurate_fullname', 150)->nullable();
            $table->string('source', 150)->nullable();
            $table->string('transaction_type', 100)->nullable();
            $table->text('transaction_description')->nullable();
            $table->string('invoice_no', 150)->nullable();
            $table->string('comp_name', 150)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('app_version', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->json('raw_payload')->nullable();
            $table->string('hash', 64)->unique();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['accurate_audit_source_id', 'accurate_audit_id'], 'audit_events_source_audit_unique');
            $table->index('accurate_audit_source_id');
            $table->index('accurate_audit_id');
            $table->index('activity_time');
            $table->index('accurate_username');
            $table->index('source');
            $table->index('transaction_type');
            $table->index('invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_audit_events');
    }
};
