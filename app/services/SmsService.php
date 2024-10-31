<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $senderId;
    protected $shortcode;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
        $this->username = config('services.sms.username');
        $this->password = config('services.sms.password');
        $this->senderId = config('services.sms.sender_id');
        $this->shortcode = config('services.sms.shortcode');
        $this->apiKey = config('services.sms.api_key');
    }

    public function send(string $phone, string $message)
    {
        try {
            $url = $this->apiUrl . '?' . http_build_query([
                'username' => $this->username,
                'password' => $this->password,
                'msg' => $message,
                'shortcode' => $this->shortcode,
                'sender_id' => $this->senderId,
                'phone' => $phone,
                'api_key' => $this->apiKey
            ]);

            $response = Http::get($url);

            // Log SMS
            SmsLog::create([
                'phone_number' => $phone,
                'message' => $message,
                'status' => $response->successful() ? 'sent' : 'failed',
                'provider_reference' => $response->json('reference') ?? null,
                'provider_response' => $response->json()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('SMS Error', [
                'message' => $e->getMessage(),
                'phone' => $phone
            ]);

            SmsLog::create([
                'phone_number' => $phone,
                'message' => $message,
                'status' => 'failed',
                'provider_response' => ['error' => $e->getMessage()]
            ]);

            return false;
        }
    }
}