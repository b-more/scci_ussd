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
    Schema::create('ussd_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('session_id')->nullable();
        $table->string('phone_number')->nullable();
        $table->string('case_no')->nullable();
        $table->string('step_no')->nullable();
        $table->string('input_message')->nullable();
        $table->string('response_message')->nullable();
        $table->enum('status', ['completed', 'incomplete', 'failed'])->default('incomplete');
        $table->timestamps();

        $table->index(['session_id', 'phone_number']);
        $table->index('status');
        $table->index('created_at');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ussd_sessions');
    }
};
