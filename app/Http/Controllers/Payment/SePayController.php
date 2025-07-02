<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SePayController extends Controller
{
    private const API_KEY = 'SJZpALmWtq2f4SWPMAdvQ2kkGih8MDRnAyhjUJ7MudEYQad5eih8bcUJupARh6s9';

    public function webhook(Request $request): JsonResponse
    {
        try {
            // Verify API key from Authorization header
            $authHeader = $request->header('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Apikey ')) {
                Log::warning('SePay webhook: Missing or invalid Authorization header', [
                    'header' => $authHeader
                ]);
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $apiKey = substr($authHeader, 7); // Remove "Apikey " prefix
            if ($apiKey !== self::API_KEY) {
                Log::warning('SePay webhook: Invalid API key', [
                    'provided_key' => $apiKey
                ]);
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Log webhook data for debugging
            Log::info('SePay webhook received', [
                'data' => $request->all()
            ]);

            // Get content and extract bill number after #
            $content = $request->input('content', '');
            if (!str_contains($content, '#')) {
                Log::warning('SePay webhook: No # found in content', [
                    'content' => $content
                ]);
                return response()->json(['message' => 'Invalid content format'], 400);
            }

            // Extract bill number after #
            $parts = explode('#', $content);
            $billNumber = trim(end($parts));

            if (empty($billNumber)) {
                Log::warning('SePay webhook: Empty bill number', [
                    'content' => $content
                ]);
                return response()->json(['message' => 'Invalid bill number'], 400);
            }

            // Find bill by ID
            $bill = Bill::find($billNumber);
            if (!$bill) {
                Log::warning('SePay webhook: Bill not found', [
                    'bill_id' => $billNumber
                ]);
                return response()->json(['message' => 'Bill not found'], 404);
            }

            // Check if bill is already paid
            if ($bill->status === Bill::STATUS_PAID) {
                Log::info('SePay webhook: Bill already paid', [
                    'bill_id' => $billNumber
                ]);
                return response()->json(['message' => 'Bill already paid'], 200);
            }

            DB::transaction(function () use ($bill, $request) {
                // Update bill status to paid
                $bill->update([
                    'status' => Bill::STATUS_PAID,
                    'payment_method' => Bill::PAYMENT_METHOD_BANK_TRANSFER
                ]);
            });

            Log::info('SePay webhook: Payment processed successfully', [
                'bill_id' => $billNumber,
                'amount' => $bill->total_amount
            ]);

            return response()->json([
                'message' => 'Payment processed successfully',
                'bill_id' => $billNumber,
                'status' => 'paid'
            ], 200);

        } catch (\Exception $e) {
            Log::error('SePay webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
