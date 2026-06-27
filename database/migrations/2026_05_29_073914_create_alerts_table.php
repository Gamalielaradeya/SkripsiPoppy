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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('log_id')->nullable()->constrained('logs')->nullOnDelete();
            $table->foreignId('accurate_audit_event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('alert_code', 100);
            $table->string('target_type', 50);
            $table->string('target_id', 100)->nullable();
            $table->string('target_name', 150);
            $table->string('category', 80);
            $table->string('severity', 30);
            $table->string('title', 200);
            $table->text('description');
            $table->string('detected_by', 150);
            $table->string('source', 150)->nullable();
            $table->text('evidence_summary')->nullable();
            $table->text('impact');
            $table->text('recommended_action');
            $table->string('status', 30)->default('open');
            $table->string('dedupe_key', 64)->nullable();
            $table->timestamp('first_detected_at')->nullable();
            $table->timestamp('last_detected_at')->nullable();
            $table->timestamp('detected_at');
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('device_id');
            $table->index('log_id');
            $table->index('accurate_audit_event_id');
            $table->index('alert_code');
            $table->index('target_type');
            $table->index('target_name');
            $table->index('category');
            $table->index('severity');
            $table->index('status');
            $table->index('detected_at');
            $table->index('dedupe_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
