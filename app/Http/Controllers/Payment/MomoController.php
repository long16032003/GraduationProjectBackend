<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MomoController extends Controller
{
    private const ACCESS_KEY = 'F8BBA842ECF85';
    private const SECRET_KEY = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';
    private const PARTNER_CODE = 'MOMO';
    private const ENDPOINT = 'https://test-payment.momo.vn/v2/gateway/api/create';
    private const ENDPOINT_QUERY = 'https://test-payment.momo.vn/v2/gateway/api/query';

    public function createPayment(Request $request): JsonResponse
    {
        try {
            // Default values
            $accessKey = self::ACCESS_KEY;
            $secretKey = self::SECRET_KEY;
            $partnerCode = self::PARTNER_CODE;
            $endpoint = self::ENDPOINT;

            // Required fields
            $orderInfo = $request->input('orderInfo', 'Thanh toán đơn hàng');
            $redirectUrl = $request->input('redirectUrl', env('MOMO_RETURN_URL'));
            $ipnUrl = $request->input('ipnUrl', env('MOMO_NOTIFY_URL'));
            $amount = $request->input('amount');
            $billId = $request->input('bill_id') ?? $request->input('orderId');
            $discountAmount = $request->input('discount_amount', 0);
            // Tạo orderId duy nhất cho mỗi lần thanh toán
            $orderId = $billId ? $billId . '_' . $discountAmount . '_' . time() . '_' . rand(1000, 9999) : time() . '_' . rand(1000, 9999);
            $requestId = time() . '_' . rand(1000, 9999);
            $requestType = 'payWithMethod';
            $extraData = $request->input('extraData', '');
            $autoCapture = $request->input('autoCapture', true);
            $lang = $request->input('lang', 'vi');
            $storeId = $request->input('storeId', 'MomoTestStore');

            // Validate required fields
            if (!$amount || $amount < 1000 || $amount > 50000000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền không hợp lệ (1.000 - 50.000.000 VND)'
                ], 400);
            }

            // Generate signature
            $rawHash = "accessKey=" . $accessKey .
                      "&amount=" . $amount .
                      "&extraData=" . $extraData .
                      "&ipnUrl=" . $ipnUrl .
                      "&orderId=" . $orderId .
                      "&orderInfo=" . $orderInfo .
                      "&partnerCode=" . $partnerCode .
                      "&redirectUrl=" . $redirectUrl .
                      "&requestId=" . $requestId .
                      "&requestType=" . $requestType;

            $signature = hash_hmac("sha256", $rawHash, $secretKey);

            $data = [
                'partnerCode' => $partnerCode,
                'storeId' => $storeId,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'requestType' => $requestType,
                'extraData' => $extraData,
                'autoCapture' => $autoCapture,
                'lang' => $lang,
                'signature' => $signature
            ];

            // Optional fields
            if ($request->has('items')) {
                $data['items'] = $request->input('items');
            }

            if ($request->has('userInfo')) {
                $data['userInfo'] = $request->input('userInfo');
            }

            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            if ($jsonResult && isset($jsonResult['payUrl'])) {
                return response()->json([
                    'success' => true,
                    'payUrl' => $jsonResult['payUrl'],
                    'orderId' => $orderId,
                    'billId' => $billId,
                    'message' => 'Tạo thanh toán thành công'
                ]);
            } else {
                Log::error('MoMo API Error', [
                    'response' => $jsonResult,
                    'request_data' => $data
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tạo thanh toán MoMo',
                    'error' => $jsonResult['message'] ?? 'Unknown error'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('MoMo Payment Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống khi tạo thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handleNotify(Request $request): JsonResponse
    {
        try {
            Log::info('MoMo IPN received', $request->all());

            // Verify signature
            $partnerCode = $request->input('partnerCode');
            $orderId = $request->input('orderId');
            $requestId = $request->input('requestId');
            $amount = $request->input('amount');
            $orderInfo = $request->input('orderInfo');
            $orderType = $request->input('orderType');
            $transId = $request->input('transId');
            $resultCode = $request->input('resultCode');
            $message = $request->input('message');
            $payType = $request->input('payType');
            $responseTime = $request->input('responseTime');
            $extraData = $request->input('extraData');
            $signature = $request->input('signature');

            // Verify signature
            $rawHash = "accessKey=" . self::ACCESS_KEY .
                      "&amount=" . $amount .
                      "&extraData=" . $extraData .
                      "&message=" . $message .
                      "&orderId=" . $orderId .
                      "&orderInfo=" . $orderInfo .
                      "&orderType=" . $orderType .
                      "&partnerCode=" . $partnerCode .
                      "&payType=" . $payType .
                      "&requestId=" . $requestId .
                      "&responseTime=" . $responseTime .
                      "&resultCode=" . $resultCode .
                      "&transId=" . $transId;

            $expectedSignature = hash_hmac("sha256", $rawHash, self::SECRET_KEY);

            if ($signature !== $expectedSignature) {
                Log::error('MoMo signature verification failed', [
                    'expected' => $expectedSignature,
                    'received' => $signature
                ]);
                return response()->json(['message' => 'Invalid signature'], 400);
            }
            // Thanh toán thành công
            if ($resultCode == 0) {
                $this->updateBillStatus($orderId, $transId, $amount);
                Log::info('MoMo payment successful', ['orderId' => $orderId, 'transId' => $transId]);
            } else {
                Log::warning('MoMo payment failed', ['orderId' => $orderId, 'resultCode' => $resultCode, 'message' => $message]);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('MoMo notify error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function handleReturn(Request $request)
    {
        try {
            $resultCode = $request->input('resultCode');
            $orderId = $request->input('orderId');
            $message = $request->input('message');

            // Trích xuất bill_id từ orderId (format: billId_timestamp_random)
            $billId = null;
            if ($orderId && strpos($orderId, '_') !== false) {
                $parts = explode('_', $orderId);
                $billId = $parts[0]; // Lấy phần đầu là bill_id
                $discountAmount = $parts[1]; // Lấy phần thứ hai là discount_amount
            } else {
                $billId = $orderId; // Fallback nếu không có format mong đợi
            }

            if ($resultCode == 0) {
                if ($billId) {
                    $bill = Bill::where('id', $billId)->first();
                    if ($bill->isUnpaid()) {
                        $bill->status = 'paid';
                        $bill->payment_method = 'momo';
                        $bill->total_amount = (int)$request->input('amount');
                        $bill->discount_amount = (int)$discountAmount;
                        $bill->save();
                    }
                }
                // return redirect('https://localhost:5173/admin/bills');
                // return response()->json([
                //     'success' => true,
                //     'message' => 'Thanh toán thành công',
                //     'orderId' => $orderId,
                //     'billId' => $billId
                // ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Thanh toán thất bại: ' . $message,
                    'orderId' => $orderId,
                    'billId' => $billId
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('MoMo return error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống'
            ], 500);
        }
    }

    public function queryPaymentStatus(Request $request): JsonResponse
    {
        try {
            $orderId = $request->input('orderId');
            $lang = $request->input('lang', 'vi');

            // Validate required fields
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã đơn hàng không được để trống'
                ], 400);
            }

            // Generate request parameters
            $accessKey = self::ACCESS_KEY;
            $secretKey = self::SECRET_KEY;
            $partnerCode = self::PARTNER_CODE;
            $requestId = time() . "";
            $endpoint = self::ENDPOINT_QUERY;

            // Generate signature for query
            $rawHash = "accessKey=" . $accessKey .
                      "&orderId=" . $orderId .
                      "&partnerCode=" . $partnerCode .
                      "&requestId=" . $requestId;

            $signature = hash_hmac("sha256", $rawHash, $secretKey);

            $data = [
                'partnerCode' => $partnerCode,
                'requestId' => $requestId,
                'orderId' => $orderId,
                'signature' => $signature,
                'lang' => $lang
            ];

            Log::info('MoMo Query Request', [
                'orderId' => $orderId,
                'requestId' => $requestId,
                'signature' => $signature
            ]);

            // Call MoMo API
            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            if ($jsonResult) {
                // Parse result
                $resultCode = $jsonResult['resultCode'] ?? -1;
                $message = $jsonResult['message'] ?? 'Không có phản hồi';

                // Log the response
                Log::info('MoMo Query Response', [
                    'orderId' => $orderId,
                    'resultCode' => $resultCode,
                    'response' => $jsonResult
                ]);

                if ($resultCode == 0) {


                    // Payment successful
                    return response()->json([
                        'success' => true,
                        'message' => 'Truy vấn thành công',
                        'data' => [
                            'orderId' => $jsonResult['orderId'] ?? $orderId,
                            'transId' => $jsonResult['transId'] ?? null,
                            'amount' => $jsonResult['amount'] ?? 0,
                            'resultCode' => $resultCode,
                            'message' => $message,
                            'payType' => $jsonResult['payType'] ?? '',
                            'responseTime' => $jsonResult['responseTime'] ?? null,
                            'paymentOption' => $jsonResult['paymentOption'] ?? '',
                            'extraData' => $jsonResult['extraData'] ?? '',
                            'promotionInfo' => $jsonResult['promotionInfo'] ?? null,
                            'refundTrans' => $jsonResult['refundTrans'] ?? []
                        ]
                    ]);
                } else {
                    // Payment failed or pending
                    $statusMessage = $this->getStatusMessage($resultCode, $lang);

                    return response()->json([
                        'success' => false,
                        'message' => $statusMessage,
                        'data' => [
                            'orderId' => $jsonResult['orderId'] ?? $orderId,
                            'resultCode' => $resultCode,
                            'message' => $message,
                            'responseTime' => $jsonResult['responseTime'] ?? null
                        ]
                    ]);
                }
            } else {
                Log::error('MoMo Query API Error: Empty response', [
                    'orderId' => $orderId,
                    'raw_response' => $result
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Không thể truy vấn trạng thái giao dịch'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('MoMo Query Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống khi truy vấn trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('CURL Error: ' . $error);
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('HTTP Error: ' . $httpCode);
        }

        return $result;
    }

    private function updateBillStatus($orderId, $transId = null, $amount)
    {
        try {
            $discountAmount = 0;
            // Trích xuất bill_id từ orderId (format: billId_timestamp_random)
            $billId = null;
            if ($orderId && strpos($orderId, '_') !== false) {
                $parts = explode('_', $orderId);
                $billId = $parts[0]; // Lấy phần đầu là bill_id
                $discountAmount = $parts[1]; // Lấy phần thứ hai là discount_amount
            } else {
                $billId = $orderId; // Fallback nếu không có format mong đợi
            }

            if (!$billId) {
                Log::error('Cannot extract billId from orderId', ['orderId' => $orderId]);
                return;
            }

            DB::transaction(function () use ($billId, $discountAmount, $amount) {
                // Update bill status
                $bill = Bill::where('id', $billId)->first();
                if (!$bill || $bill->isPaid()) {
                    return;
                }
                $bill->status = 'paid';
                $bill->payment_method = 'momo';
                $bill->total_amount = (int)$amount;
                $bill->discount_amount = (int)$discountAmount;
                $bill->save();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update payment status', [
                'orderId' => $orderId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getStatusMessage($resultCode, $lang = 'vi')
    {
        $messages = [
            'vi' => [
                0 => 'Giao dịch thành công',
                9 => 'Giao dịch được cấp quyền thành công',
                10 => 'Giao dịch đang được xử lý',
                11 => 'Giao dịch đã được xác nhận nhưng chưa hoàn tất',
                12 => 'Giao dịch đã hết hạn',
                13 => 'Giao dịch đã bị hủy',
                20 => 'Giao dịch thất bại',
                21 => 'Số dư tài khoản không đủ',
                22 => 'Giao dịch bị từ chối',
                23 => 'Giao dịch bị tạm hoãn',
                40 => 'Giao dịch đang chờ xử lý',
                41 => 'Giao dịch đang chờ xác nhận',
                42 => 'Giao dịch đang chờ cấp quyền',
                43 => 'Giao dịch đang chờ xác nhận OTP',
                99 => 'Giao dịch không xác định',
                1000 => 'Giao dịch đã được khởi tạo',
                1001 => 'Giao dịch đang chờ thanh toán',
                1002 => 'Giao dịch đang chờ xác nhận',
                1003 => 'Giao dịch đã bị hủy',
                1004 => 'Giao dịch đã hết hạn',
                1005 => 'Giao dịch đang chờ hoàn tiền',
                1006 => 'Giao dịch đã được hoàn tiền',
                2001 => 'Giao dịch thất bại do sai thông tin',
                2002 => 'Giao dịch thất bại do hệ thống',
                2003 => 'Giao dịch thất bại do chưa xác thực',
                2004 => 'Giao dịch thất bại do số tiền không hợp lệ',
                2005 => 'Giao dịch thất bại do nhà cung cấp',
                2006 => 'Giao dịch thất bại do vượt quá hạn mức',
                2007 => 'Giao dịch thất bại do không đủ quyền hạn',
                2008 => 'Giao dịch thất bại do tài khoản bị khóa',
                3001 => 'Giao dịch đang chờ xử lý từ ngân hàng',
                3002 => 'Giao dịch đang chờ xác nhận từ ngân hàng',
                3003 => 'Giao dịch bị từ chối từ ngân hàng',
                3004 => 'Giao dịch thất bại từ ngân hàng',
                3005 => 'Giao dịch hết hạn từ ngân hàng',
                3006 => 'Giao dịch đã được hoàn tiền từ ngân hàng',
                3007 => 'Giao dịch đang chờ hoàn tiền từ ngân hàng',
                4001 => 'Giao dịch thất bại do nhà cung cấp dịch vụ',
                4002 => 'Giao dịch thất bại do mạng',
                4003 => 'Giao dịch thất bại do bảo trì hệ thống',
                4100 => 'Giao dịch bị từ chối do chính sách'
            ],
            'en' => [
                0 => 'Successful transaction',
                9 => 'Transaction authorized successfully',
                10 => 'Transaction is being processed',
                11 => 'Transaction confirmed but not completed',
                12 => 'Transaction expired',
                13 => 'Transaction cancelled',
                20 => 'Transaction failed',
                21 => 'Insufficient account balance',
                22 => 'Transaction rejected',
                23 => 'Transaction suspended',
                40 => 'Transaction pending',
                41 => 'Transaction awaiting confirmation',
                42 => 'Transaction awaiting authorization',
                43 => 'Transaction awaiting OTP confirmation',
                99 => 'Unknown transaction',
                1000 => 'Transaction initialized',
                1001 => 'Transaction awaiting payment',
                1002 => 'Transaction awaiting confirmation',
                1003 => 'Transaction cancelled',
                1004 => 'Transaction expired',
                1005 => 'Transaction awaiting refund',
                1006 => 'Transaction refunded',
                2001 => 'Transaction failed due to incorrect information',
                2002 => 'Transaction failed due to system error',
                2003 => 'Transaction failed due to unauthenticated',
                2004 => 'Transaction failed due to invalid amount',
                2005 => 'Transaction failed due to provider error',
                2006 => 'Transaction failed due to exceeding limit',
                2007 => 'Transaction failed due to insufficient permissions',
                2008 => 'Transaction failed due to account locked',
                3001 => 'Transaction pending from bank',
                3002 => 'Transaction awaiting confirmation from bank',
                3003 => 'Transaction rejected by bank',
                3004 => 'Transaction failed from bank',
                3005 => 'Transaction expired from bank',
                3006 => 'Transaction refunded from bank',
                3007 => 'Transaction awaiting refund from bank',
                4001 => 'Transaction failed due to service provider',
                4002 => 'Transaction failed due to network error',
                4003 => 'Transaction failed due to system maintenance',
                4100 => 'Transaction rejected due to policy'
            ]
        ];

        $langMessages = $messages[$lang] ?? $messages['vi'];

        return $langMessages[$resultCode] ?? ($lang === 'vi' ? 'Mã lỗi không xác định: ' . $resultCode : 'Unknown error code: ' . $resultCode);
    }
}
