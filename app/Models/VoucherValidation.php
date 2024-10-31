<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherValidation extends Model
{
    protected $fillable = [
        'voucher_number',
        'phone_number',
        'status',
        'scci_response',
        'seed_company',
        'seed_type',
        'batch_number',
        'validation_date'
    ];

    protected $casts = [
        'scci_response' => 'array',
        'validation_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class);
    }
}
