<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'voucher_number',
        'batch_number',
        'seed_type',
        'seed_variety',
        'seed_class',
        'quantity_kg',
        'seed_company_name',
        'seed_company_license',
        'production_date',
        'testing_date',
        'packaging_date',
        'laboratory_test_number',
        'germination_rate',
        'purity_rate',
        'moisture_content',
        'valid_from',
        'valid_until',
        'is_active',
        'is_used',
        'used_at',
        'used_by_phone',
        'verification_attempts',
        'last_verification_attempt',
        'region',
        'district',
        'distribution_point',
        'created_by',
        'approved_by',
        'approved_at',
        'comments',
        'status'
    ];

    protected $casts = [
        'production_date' => 'date',
        'testing_date' => 'date',
        'packaging_date' => 'date',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'used_at' => 'datetime',
        'last_verification_attempt' => 'datetime',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
        'is_used' => 'boolean',
        'verification_attempts' => 'integer',
    ];

    // Scopes for common queries
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', now());
                    });
    }

    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    // Check if voucher is valid
    public function isValid()
    {
        return $this->is_active 
            && !$this->is_used 
            && $this->status === 'active'
            && ($this->valid_until === null || $this->valid_until >= now());
    }

    // Record a verification attempt
    public function recordVerificationAttempt($phone)
    {
        $this->increment('verification_attempts');
        $this->update([
            'last_verification_attempt' => now()
        ]);
    }

    // Mark voucher as used
    public function markAsUsed($phone)
    {
        return $this->update([
            'is_used' => true,
            'used_at' => now(),
            'used_by_phone' => $phone,
            'status' => 'used'
        ]);
    }
}