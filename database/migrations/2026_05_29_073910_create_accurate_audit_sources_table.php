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
        Schema::create('accurate_audit_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('firebird_host', 150);
            $table->unsignedInteger('firebird_port')->default(3051);
            $table->string('database_path', 500);
            $table->string('username', 100)->nullable();
            $table->string('credential_ref', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('last_connection_status', 30)->default('unknown');
            $table->text('last_connection_error')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('firebird_host');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_audit_sources');
    }
};
