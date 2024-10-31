<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message',
        'status',
        'provider_reference',
        'provider_response'
    ];

    protected $casts = [
        'provider_response' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
