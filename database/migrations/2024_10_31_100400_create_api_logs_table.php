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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->json('request_data');
            $table->json('response_data')->nullable();
            $table->integer('response_code')->nullable();
            $table->float('response_time')->nullable(); // in seconds
            $table->enum('status', ['success', 'failed']);
            $table->timestamps();

            $table->index('endpoint');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
