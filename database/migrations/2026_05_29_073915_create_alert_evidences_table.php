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
        Schema::create('alert_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained()->cascadeOnDelete();
            $table->string('evidence_key', 100);
            $table->text('evidence_value')->nullable();
            $table->string('evidence_type', 30)->default('text');
            $table->string('source', 150)->nullable();
            $table->timestamp('measured_at')->nullable();
            $table->timestamps();

            $table->index('alert_id');
            $table->index('evidence_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_evidences');
    }
};
