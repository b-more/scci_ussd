<?php

namespace App\Http\Controllers;

use App\Models\UssdSession;
use App\Models\VoucherValidation;
use App\Services\ScciApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UssdSessionController extends Controller
{
    protected $scciApi;

    public function __construct(ScciApiService $scciApi)
    {
        $this->scciApi = $scciApi;
    }

    public function reapay(Request $request)
    {

        Log::info('USSD Request received', [
            'path' => $request->path(),
            'method' => $request->method(),
            'data' => $request->all()
        ]);
        // Initialize variables
        $message_string = "";
        $case_no = 1;
        $step_no = 1;
        $phone = $request->MSISDN;
        $user_input = $request->MESSAGE;
        $session_id = $request->SESSION_ID;
        $request_type = "2"; // Continue

        // Log USSD request
        Log::info("USSD Request", [
            "input" => $user_input, 
            "phone" => $phone,
            "session" => $session_id
        ]);

        // Parse USSD string
        $parts = explode("*", $user_input);
        $last_part = end($parts);

        // Handle direct voucher validation via **388*4*voucher_no#
        if (count($parts) === 4 && $parts[1] === "388" && $parts[2] === "4") {
            $voucher_no = trim($parts[3], '#');
            Log::info("Direct voucher validation", ["voucher_no" => $voucher_no]);
            
            // Create new session
            UssdSession::create([
                "session_id" => $session_id,
                "phone_number" => $phone,
                "case_no" => "1",
                "step_no" => "2",
                "input_message" => $user_input,
                "status" => "incomplete"
            ]);

            // Validate voucher with SCCI
            $validationResult = $this->scciApi->validateVoucher($voucher_no, $phone);

            if (!$validationResult) {
                $message = "Unable to validate voucher at this time. Please try again later.";
                $this->updateSessionAndSendSms($session_id, $message, 'failed', $phone);
                return $this->sendResponse($message, '3');
            }

            // Record validation
            VoucherValidation::create([
                'voucher_number' => $voucher_no,
                'phone_number' => $phone,
                'status' => $validationResult['status'],
                'scci_response' => $validationResult,
                'seed_company' => $validationResult['seed_company'] ?? null,
                'seed_type' => $validationResult['seed_type'] ?? null,
                'batch_number' => $validationResult['batch_number'] ?? null,
                'validation_date' => Carbon::now()
            ]);

            $message = $this->getValidationMessage($validationResult);
            $this->updateSessionAndSendSms($session_id, $message, 'completed', $phone);
            return $this->sendResponse($message, '3');
        }

        // Retrieve last session information
        $lastSession = UssdSession::where('phone_number', $phone)
            ->where('session_id', $session_id)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastSession) {
            $case_no = $lastSession->case_no;
            $step_no = $lastSession->step_no;
        } else {
            // Create new session
            UssdSession::create([
                "session_id" => $session_id,
                "phone_number" => $phone,
                "case_no" => "1",
                "step_no" => "1",
                "input_message" => $user_input,
                "status" => "incomplete"
            ]);
        }

        // Main menu flow
        switch ($case_no) {
            case '1':
                if ($case_no == 1 && $step_no == 1) {
                    $message_string = "Welcome to SCCI Seed Verification:\n1. Verify Seed Voucher\n2. Report Fake Seed\n3. Exit";
                    UssdSession::where('session_id', $session_id)
                        ->update([
                            "case_no" => "1", 
                            "step_no" => "2",
                            "response_message" => $message_string
                        ]);

                } elseif ($case_no == 1 && $step_no == 2 && is_numeric($last_part)) {
                    if ($last_part == 1) {
                        $message_string = "Please enter the voucher number:";
                        UssdSession::where('session_id', $session_id)
                            ->update([
                                "step_no" => "3",
                                "response_message" => $message_string
                            ]);
                    } elseif ($last_part == 2) {
                        $message_string = "To report fake seed, please call our toll-free number: 789\nOr email: report@scci.gov.zm";
                        $this->updateSessionAndSendSms($session_id, $message_string, 'completed', $phone);
                        $request_type = "3";
                    } elseif ($last_part == 3) {
                        $message_string = "Thank you for using SCCI Seed Verification service.";
                        $this->updateSessionAndSendSms($session_id, $message_string, 'completed', $phone);
                        $request_type = "3";
                    } else {
                        $message_string = "Invalid option. Please try again.";
                        UssdSession::where('session_id', $session_id)
                            ->update([
                                "step_no" => "1",
                                "response_message" => $message_string
                            ]);
                    }
                } elseif ($case_no == 1 && $step_no == 3) {
                    $voucher_no = trim($last_part, '#');
                    
                    // Validate voucher with SCCI
                    $validationResult = $this->scciApi->validateVoucher($voucher_no, $phone);

                    if (!$validationResult) {
                        $message_string = "Unable to validate voucher at this time. Please try again later.";
                        $this->updateSessionAndSendSms($session_id, $message_string, 'failed', $phone);
                        return $this->sendResponse($message_string, '3');
                    }

                    // Record validation
                    VoucherValidation::create([
                        'voucher_number' => $voucher_no,
                        'phone_number' => $phone,
                        'status' => $validationResult['status'],
                        'scci_response' => $validationResult,
                        'seed_company' => $validationResult['seed_company'] ?? null,
                        'seed_type' => $validationResult['seed_type'] ?? null,
                        'batch_number' => $validationResult['batch_number'] ?? null,
                        'validation_date' => Carbon::now()
                    ]);

                    $message_string = $this->getValidationMessage($validationResult);
                    $this->updateSessionAndSendSms($session_id, $message_string, 'completed', $phone);
                    $request_type = "3";
                }
                break;
        }

        return response()->json([
            "ussd_response" => [
                "USSD_BODY" => $message_string,
                "REQUEST_TYPE" => $request_type
            ]
        ]);
    }

    private function getValidationMessage($validationResult)
    {
        switch ($validationResult['status']) {
            case 'valid':
                return "Valid voucher. This is genuine {$validationResult['seed_type']} seed from {$validationResult['seed_company']}.";
            case 'used':
                return "This voucher has already been used on {$validationResult['used_date']}.";
            case 'expired':
                return "This voucher has expired. Please contact the seller.";
            case 'invalid':
                return "Invalid voucher. This may be counterfeit seed. Please report to SCCI.";
            default:
                return "Unable to verify voucher. Please try again later.";
        }
    }

    private function updateSessionAndSendSms($session_id, $message, $status, $phone)
    {
        UssdSession::where('session_id', $session_id)->update([
            "status" => $status,
            "response_message" => $message
        ]);

        $this->sendNotification($phone, $message);
    }

    private function sendNotification($phone, $message_string): void
    {
        $url_encoded_message = urlencode($message_string);
        $url = 'https://www.cloudservicezm.com/smsservice/httpapi?' . http_build_query([
            'username' => 'Blessmore',
            'password' => 'Blessmore',
            'msg' => $message_string . '.',
            'shortcode' => '2343',
            'sender_id' => 'REA',
            'phone' => $phone,
            'api_key' => '121231313213123123'
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        
        Log::info("SMS Notification sent", [
            "phone" => $phone,
            "response" => $response
        ]);
    }

    private function sendResponse($message, $requestType)
    {
        return response()->json([
            'ussd_response' => [
                'USSD_BODY' => $message,
                'REQUEST_TYPE' => $requestType
            ]
        ]);
    }
}