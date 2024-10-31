<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'endpoint',
        'request_data',
        'response_data',
        'response_code',
        'response_time',
        'status'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'response_time' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}