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
        Schema::create('accurate_audit_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accurate_audit_source_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_rows_read')->default(0);
            $table->unsignedInteger('total_inserted')->default(0);
            $table->unsignedInteger('total_duplicates')->default(0);
            $table->string('status', 30)->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index('accurate_audit_source_id');
            $table->index('status');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_audit_sync_runs');
    }
};
