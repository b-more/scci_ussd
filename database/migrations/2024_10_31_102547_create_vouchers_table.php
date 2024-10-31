<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            
            // Voucher Information
            $table->string('voucher_number')->unique();
            $table->string('batch_number')->nullable();
            $table->string('seed_type')->nullable();
            $table->string('seed_variety')->nullable();
            $table->string('seed_class')->nullable(); // e.g., Certified, Basic, etc.
            $table->decimal('quantity_kg', 10, 2)->nullable();
            
            // Seed Company Information
            $table->string('seed_company_name')->nullable();
            $table->string('seed_company_license')->nullable();
            
            // Production and Testing
            $table->date('production_date')->nullable();
            $table->date('testing_date')->nullable();
            $table->date('packaging_date')->nullable();
            $table->string('laboratory_test_number')->nullable();
            $table->string('germination_rate')->nullable();
            $table->string('purity_rate')->nullable();
            $table->string('moisture_content')->nullable();
            
            // Validity Information
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Usage Tracking
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->string('used_by_phone')->nullable();
            $table->integer('verification_attempts')->default(0);
            $table->dateTime('last_verification_attempt')->nullable();
            
            // Location Information
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('distribution_point')->nullable();
            
            // Administrative
            $table->string('created_by')->nullable(); // User who created the voucher
            $table->string('approved_by')->nullable(); // SCCI official who approved
            $table->timestamp('approved_at')->nullable();
            $table->text('comments')->nullable();
            $table->enum('status', ['active', 'used', 'expired', 'suspended'])->default('active');
            $table->softDeletes(); // Adds deleted_at column
            $table->timestamps();

            // Indexes for performance
            $table->index('voucher_number');
            $table->index('batch_number');
            $table->index('seed_type');
            $table->index('seed_company_name');
            $table->index('is_used');
            $table->index('status');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};