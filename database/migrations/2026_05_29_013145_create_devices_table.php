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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id', 64)->unique();
            $table->string('hostname', 100);
            $table->string('device_label', 150)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('os_name', 100)->nullable();
            $table->string('os_version', 100)->nullable();
            $table->string('ip_zerotier', 45)->nullable();
            $table->string('ip_local', 45)->nullable();
            $table->string('mac_address', 50)->nullable();
            $table->string('windows_user', 150)->nullable();
            $table->string('agent_version', 50)->nullable();
            $table->string('agent_status', 30)->default('unknown');
            $table->string('rdp_status', 30)->default('unknown');
            $table->string('accurate_status', 30)->default('unknown');
            $table->string('firebird_connection_status', 30)->default('unknown');
            $table->string('status', 30)->default('unknown');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('hostname');
            $table->index('status');
            $table->index('agent_status');
            $table->index('last_seen_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
