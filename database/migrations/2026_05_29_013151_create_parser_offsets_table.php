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
        Schema::create('parser_offsets', function (Blueprint $table) {
            $table->id();
            $table->string('source_file', 255)->unique();
            $table->unsignedBigInteger('last_position')->default(0);
            $table->string('last_line_hash', 64)->nullable();
            $table->timestamp('last_parsed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parser_offsets');
    }
};
