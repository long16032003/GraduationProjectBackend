<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_ReturnUrl;
    private $vnp_IpnUrl;

    public function __construct()
    {
        $this->vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $this->vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $this->vnp_Url = config('vnpay.vnp_Url');
        $this->vnp_ReturnUrl = config('vnpay.vnp_ReturnUrl');
        $this->vnp_IpnUrl = config('vnpay.vnp_IpnUrl');
    }

    /**
     * Create payment URL for VNPay
     */
    public function createPaymentUrl(Bill $bill, string $ipAddr = null): array
    {
        try {
            // Generate transaction reference
            $vnp_TxnRef = Payment::generateTxnRef();

            // Create payment record
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'vnp_TxnRef' => $vnp_TxnRef,
                'vnp_Amount' => $bill->total_amount,
                'vnp_OrderInfo' => "Thanh toan hoa don #{$bill->id}",
                'status' => Payment::STATUS_PENDING,
            ]);

            $vnp_Amount = $bill->total_amount * 100; // VNPay requires amount in smallest unit

            $inputData = [
                "vnp_Version" => config('vnpay.vnp_Version'),
                "vnp_TmnCode" => $this->vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => config('vnpay.vnp_Command'),
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => config('vnpay.vnp_CurrCode'),
                "vnp_IpAddr" => $ipAddr ?? request()->ip(),
                "vnp_Locale" => config('vnpay.vnp_Locale'),
                "vnp_OrderInfo" => $payment->vnp_OrderInfo,
                "vnp_OrderType" => config('vnpay.vnp_OrderType'),
                "vnp_ReturnUrl" => $this->vnp_ReturnUrl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')), // Format: yyyyMMddHHmmss
            ];

            // Add IPN URL if configured
            if ($this->vnp_IpnUrl) {
                $inputData['vnp_IpnUrl'] = $this->vnp_IpnUrl;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $this->vnp_Url . "?" . $query;
            if (isset($this->vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            Log::info('VNPay Payment URL created', [
                'bill_id' => $bill->id,
                'payment_id' => $payment->id,
                'vnp_TxnRef' => $vnp_TxnRef,
                'amount' => $bill->total_amount
            ]);

            return [
                'code' => '00',
                'message' => 'success',
                'data' => [
                    'payment_id' => $payment->id,
                    'payment_url' => $vnp_Url,
                    'vnp_TxnRef' => $vnp_TxnRef,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('VNPay Payment URL creation failed', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return [
                'code' => '99',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Verify return from VNPay
     */
    public function verifyReturnUrl(array $vnpayData): array
    {
        try {
            $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';
            unset($vnpayData['vnp_SecureHash']);
            unset($vnpayData['vnp_SecureHashType']);

            ksort($vnpayData);
            $hashdata = "";
            $i = 0;
            foreach ($vnpayData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

            $vnp_TxnRef = $vnpayData['vnp_TxnRef'] ?? '';
            $vnp_Amount = $vnpayData['vnp_Amount'] ?? 0;
            $vnp_ResponseCode = $vnpayData['vnp_ResponseCode'] ?? '';

            // Find payment record
            $payment = Payment::where('vnp_TxnRef', $vnp_TxnRef)->first();

            if (!$payment) {
                return [
                    'code' => '01',
                    'message' => 'Không tìm thấy giao dịch',
                    'data' => null
                ];
            }

            // Verify secure hash
            if ($secureHash !== $vnp_SecureHash) {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'vnpay_response' => $vnpayData
                ]);

                return [
                    'code' => '97',
                    'message' => 'Chữ ký không hợp lệ',
                    'data' => $payment
                ];
            }

            // Verify amount
            $originalAmount = $payment->vnp_Amount * 100;
            if ($originalAmount != $vnp_Amount) {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'vnpay_response' => $vnpayData
                ]);

                return [
                    'code' => '04',
                    'message' => 'Số tiền không hợp lệ',
                    'data' => $payment
                ];
            }

            // Update payment with VNPay response
            $payment->update([
                'vnp_TransactionNo' => $vnpayData['vnp_TransactionNo'] ?? null,
                'vnp_ResponseCode' => $vnp_ResponseCode,
                'vnp_TransactionStatus' => $vnpayData['vnp_TransactionStatus'] ?? null,
                'vnp_PayDate' => $vnpayData['vnp_PayDate'] ?? null,
                'vnp_BankCode' => $vnpayData['vnp_BankCode'] ?? null,
                'vnp_BankTranNo' => $vnpayData['vnp_BankTranNo'] ?? null,
                'vnp_CardType' => $vnpayData['vnp_CardType'] ?? null,
                'vnpay_response' => $vnpayData
            ]);

            // Update payment status
            $payment->updateStatusFromVnpay($vnp_ResponseCode);

            // Update bill status if payment successful
            if ($payment->isSuccess()) {
                $payment->bill->update(['status' => Bill::STATUS_PAID]);

                Log::info('VNPay Payment successful', [
                    'bill_id' => $payment->bill_id,
                    'payment_id' => $payment->id,
                    'vnp_TxnRef' => $vnp_TxnRef,
                    'amount' => $payment->vnp_Amount
                ]);
            }

            return [
                'code' => '00',
                'message' => $this->getResponseMessage($vnp_ResponseCode),
                'data' => $payment->load('bill')
            ];

        } catch (\Exception $e) {
            Log::error('VNPay Return verification failed', [
                'error' => $e->getMessage(),
                'data' => $vnpayData
            ]);

            return [
                'code' => '99',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Handle IPN from VNPay
     */
    public function handleIPN(array $vnpayData): array
    {
        try {
            $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';
            unset($vnpayData['vnp_SecureHash']);
            unset($vnpayData['vnp_SecureHashType']);

            ksort($vnpayData);
            $hashdata = "";
            $i = 0;
            foreach ($vnpayData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

            $vnp_TxnRef = $vnpayData['vnp_TxnRef'] ?? '';
            $vnp_Amount = $vnpayData['vnp_Amount'] ?? 0;
            $vnp_ResponseCode = $vnpayData['vnp_ResponseCode'] ?? '';

            // Find payment record
            $payment = Payment::where('vnp_TxnRef', $vnp_TxnRef)->first();

            if (!$payment) {
                return ['RspCode' => '01', 'Message' => 'Order not found'];
            }

            // Verify secure hash
            if ($secureHash !== $vnp_SecureHash) {
                return ['RspCode' => '97', 'Message' => 'Invalid signature'];
            }

            // Verify amount
            $originalAmount = $payment->vnp_Amount * 100;
            if ($originalAmount != $vnp_Amount) {
                return ['RspCode' => '04', 'Message' => 'Invalid amount'];
            }

            // Check if already processed
            if ($payment->status !== Payment::STATUS_PENDING) {
                return ['RspCode' => '02', 'Message' => 'Order already confirmed'];
            }

            // Update payment with VNPay response
            $payment->update([
                'vnp_TransactionNo' => $vnpayData['vnp_TransactionNo'] ?? null,
                'vnp_ResponseCode' => $vnp_ResponseCode,
                'vnp_TransactionStatus' => $vnpayData['vnp_TransactionStatus'] ?? null,
                'vnp_PayDate' => $vnpayData['vnp_PayDate'] ?? null,
                'vnp_BankCode' => $vnpayData['vnp_BankCode'] ?? null,
                'vnp_BankTranNo' => $vnpayData['vnp_BankTranNo'] ?? null,
                'vnp_CardType' => $vnpayData['vnp_CardType'] ?? null,
                'vnpay_response' => $vnpayData
            ]);

            // Update payment status
            $payment->updateStatusFromVnpay($vnp_ResponseCode);

            // Update bill status if payment successful
            if ($payment->isSuccess()) {
                $payment->bill->update(['status' => Bill::STATUS_PAID]);
            }

            Log::info('VNPay IPN processed', [
                'bill_id' => $payment->bill_id,
                'payment_id' => $payment->id,
                'vnp_TxnRef' => $vnp_TxnRef,
                'status' => $payment->status
            ]);

            return ['RspCode' => '00', 'Message' => 'Confirm Success'];

        } catch (\Exception $e) {
            Log::error('VNPay IPN processing failed', [
                'error' => $e->getMessage(),
                'data' => $vnpayData
            ]);

            return ['RspCode' => '99', 'Message' => 'Unknown error'];
        }
    }

    /**
     * Get response message based on VNPay response code
     */
    private function getResponseMessage(string $responseCode): string
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường)',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}
