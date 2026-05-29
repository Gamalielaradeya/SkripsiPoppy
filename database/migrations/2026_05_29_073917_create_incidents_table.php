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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('incident_code', 100);
            $table->string('target_type', 50);
            $table->string('target_id', 100)->nullable();
            $table->string('target_name', 150);
            $table->string('severity', 30);
            $table->string('title', 200);
            $table->text('summary');
            $table->json('evidence_json')->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamp('detected_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('device_id');
            $table->index('incident_code');
            $table->index('target_type');
            $table->index('severity');
            $table->index('status');
            $table->index('detected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
