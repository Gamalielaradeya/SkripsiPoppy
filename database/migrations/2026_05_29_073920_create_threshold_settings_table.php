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
        Schema::create('threshold_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 80);
            $table->string('key', 120)->unique();
            $table->string('value', 255);
            $table->string('data_type', 30)->default('integer');
            $table->string('description', 255)->nullable();
            $table->boolean('is_editable')->default(true);
            $table->timestamps();

            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threshold_settings');
    }
};
