<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        return response()->json([
            'message' => 'Test endpoint working',
            'timestamp' => now(),
            'request_info' => [
                'path' => request()->path(),
                'method' => request()->method(),
                'headers' => request()->headers->all(),
            ]
        ]);
    }
}