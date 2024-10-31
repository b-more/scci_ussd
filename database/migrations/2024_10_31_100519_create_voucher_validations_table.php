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
        Schema::create('voucher_validations', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number');
            $table->string('phone_number');
            $table->enum('status', ['valid', 'invalid', 'used', 'expired']);
            $table->json('scci_response')->nullable(); // Store complete response from SCCI
            $table->string('seed_company')->nullable();
            $table->string('seed_type')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamp('validation_date');
            $table->timestamps();

            $table->index('voucher_number');
            $table->index('phone_number');
            $table->index('status');
            $table->index('validation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_validations');
    }
};
