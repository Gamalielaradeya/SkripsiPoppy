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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('agent_id', 64)->nullable();
            $table->string('hostname', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('facility', 50)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('event_type', 80)->nullable();
            $table->string('category', 80)->nullable();
            $table->string('severity', 30)->default('info');
            $table->string('target_type', 50)->nullable();
            $table->string('target_id', 100)->nullable();
            $table->string('target_name', 150)->nullable();
            $table->text('raw_message');
            $table->text('parsed_message')->nullable();
            $table->json('parsed_payload')->nullable();
            $table->string('source_file', 500)->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->string('hash', 64);
            $table->timestamps();

            $table->unique('hash');
            $table->index(['device_id', 'logged_at']);
            $table->index(['source', 'event_type']);
            $table->index(['category', 'severity']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
