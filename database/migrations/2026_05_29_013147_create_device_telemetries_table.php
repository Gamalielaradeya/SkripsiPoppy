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
        Schema::create('device_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('agent_id', 64);
            $table->string('hostname', 100)->nullable();
            $table->string('windows_user', 150)->nullable();
            $table->string('ip_zerotier', 45)->nullable();
            $table->string('ip_local', 45)->nullable();
            $table->decimal('cpu_usage_percent', 5, 2)->nullable();
            $table->decimal('ram_usage_percent', 5, 2)->nullable();
            $table->decimal('disk_usage_percent', 5, 2)->nullable();
            $table->unsignedBigInteger('uptime_seconds')->nullable();
            $table->timestamp('last_boot_at')->nullable();
            $table->string('agent_status', 30)->default('unknown');
            $table->json('raw_payload')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'reported_at']);
            $table->index('agent_id');
            $table->index('agent_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_telemetries');
    }
};
