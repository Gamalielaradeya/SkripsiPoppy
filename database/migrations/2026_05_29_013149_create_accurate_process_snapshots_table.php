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
        Schema::create('accurate_process_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('process_name', 100)->default('accurate.exe');
            $table->string('process_status', 30)->default('unknown');
            $table->unsignedInteger('process_pid')->nullable();
            $table->string('process_owner', 150)->nullable();
            $table->string('process_path', 500)->nullable();
            $table->timestamp('process_started_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'checked_at']);
            $table->index('process_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_process_snapshots');
    }
};
