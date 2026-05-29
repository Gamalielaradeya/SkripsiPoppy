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
        Schema::create('network_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('target_type', 50);
            $table->string('target_name', 150)->nullable();
            $table->string('target_host', 255)->nullable();
            $table->unsignedInteger('target_port')->nullable();
            $table->string('ping_status', 30)->default('unknown');
            $table->decimal('ping_latency_ms', 8, 2)->nullable();
            $table->string('tcp_status', 30)->default('unknown');
            $table->decimal('tcp_latency_ms', 8, 2)->nullable();
            $table->decimal('packet_loss_percent', 5, 2)->nullable();
            $table->string('status', 30)->default('unknown');
            $table->json('raw_payload')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'checked_at']);
            $table->index(['target_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_checks');
    }
};
