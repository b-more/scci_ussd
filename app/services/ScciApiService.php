<?php

namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScciApiService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.scci.url');
        $this->apiKey = config('services.scci.key');
    }

    public function validateVoucher(string $voucherNumber, string $phoneNumber)
    {
        $startTime = microtime(true);
        
        try {
            $requestData = [
                'voucher_number' => $voucherNumber,
                'phone_number' => $phoneNumber,
                'api_key' => $this->apiKey
            ];

            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/validate-voucher", $requestData);

            $endTime = microtime(true);

            // Log API communication
            ApiLog::create([
                'endpoint' => 'validate-voucher',
                'request_data' => $requestData,
                'response_data' => $response->json(),
                'response_code' => $response->status(),
                'response_time' => $endTime - $startTime,
                'status' => $response->successful() ? 'success' : 'failed'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('SCCI API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('SCCI API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            ApiLog::create([
                'endpoint' => 'validate-voucher',
                'request_data' => $requestData ?? [],
                'status' => 'failed',
                'response_time' => microtime(true) - $startTime
            ]);

            return null;
        }
    }
}