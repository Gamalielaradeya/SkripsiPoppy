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
        Schema::create('server_service_checks', function (Blueprint $table) {
            $table->id();
            $table->string('server_name', 150);
            $table->string('service_name', 150);
            $table->string('service_display_name', 255)->nullable();
            $table->string('service_status', 30)->default('unknown');
            $table->unsignedInteger('port')->nullable();
            $table->string('port_status', 30)->default('unknown');
            $table->string('status', 30)->default('unknown');
            $table->text('raw_output')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index(['server_name', 'service_name']);
            $table->index(['status', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_service_checks');
    }
};
