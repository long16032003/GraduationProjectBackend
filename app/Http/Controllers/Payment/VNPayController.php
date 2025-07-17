<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Payment;
use App\Services\VNPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VNPayController extends Controller
{
    private VNPayService $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    /**
     * Create payment URL for VNPay
     */
    public function createPayment(Request $request): JsonResponse
    {
        try {
            Log::info('VNPay Payment Request', [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ]);

            $validator = Validator::make($request->all(), [
                'bill_id' => 'required|integer|exists:bills,id',
                'amount' => 'required|numeric|min:0',
                'coupon_code' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::warning('VNPay Payment Validation Failed', [
                    'errors' => $validator->errors(),
                    'request_data' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bill = Bill::findOrFail($request->bill_id);
            $result = $bill->update([
                'total_amount' => $request->amount,
                'payment_method' => 'cash'
            ]);

            if($result && $bill->customer_phone) {
                $customer = Customer::where('phone', $bill->customer_phone)->first();
                if($customer && $bill->total_amount > 10000) {
                    $customer->update([
                        'point' => $customer->point + round((int)$bill->total_amount / 10000),
                    ]);
                }
            }

            // Check if bill already paid
            if ($bill->status === Bill::STATUS_PAID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hóa đơn đã được thanh toán'
                ], 400);
            }


            // Check if there's already a pending payment
            $pendingPayment = $bill->payments()->where('status', Payment::STATUS_PENDING)->first();
            if ($pendingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã có giao dịch đang chờ xử lý cho hóa đơn này',
                    'data' => [
                        'payment_id' => $pendingPayment->id,
                        'vnp_TxnRef' => $pendingPayment->vnp_TxnRef
                    ]
                ], 400);
            }

            $result = $this->vnpayService->createPaymentUrl($bill, $request->ip());

            if ($result['code'] === '00') {
                return response()->json([
                    'success' => true,
                    'message' => 'Tạo link thanh toán thành công',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('VNPay Payment creation failed', [
                'bill_id' => $request->bill_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Có lỗi xảy ra khi tạo thanh toán'
            ], 500);
        }
    }

    /**
     * Handle return from VNPay
     */
    public function returnUrl(Request $request): JsonResponse
    {
        try {
            $vnpayData = $request->all();
            $result = $this->vnpayService->verifyReturnUrl($vnpayData);

            if ($result['code'] === '00') {
                $payment = $result['data'];

                return new JsonResponse([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'payment_id' => $payment->id,
                        'bill_id' => $payment->bill_id,
                        'status' => $payment->status,
                        'amount' => $payment->vnp_Amount,
                        'vnp_TxnRef' => $payment->vnp_TxnRef,
                        'vnp_TransactionNo' => $payment->vnp_TransactionNo,
                        'vnp_ResponseCode' => $payment->vnp_ResponseCode,
                        'is_success' => $payment->isSuccess(),
                        'bill' => $payment->bill
                    ]
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'data' => $result['data']
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $e) {
            Log::error('VNPay Return URL processing failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý kết quả thanh toán'
            ], 500);
        }
    }

    /**
     * Handle IPN (Instant Payment Notification) from VNPay
     */
    public function ipn(Request $request): JsonResponse
    {
        try {
            $vnpayData = $request->all();
            $result = $this->vnpayService->handleIPN($vnpayData);

            Log::info('VNPay IPN received', [
                'request_data' => $vnpayData,
                'result' => $result
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('VNPay IPN processing failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'RspCode' => '99',
                'Message' => 'Unknown error'
            ]);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request, $paymentId): JsonResponse
    {
        try {
            $payment = Payment::with('bill')->findOrFail($paymentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'bill_id' => $payment->bill_id,
                    'status' => $payment->status,
                    'amount' => $payment->vnp_Amount,
                    'vnp_TxnRef' => $payment->vnp_TxnRef,
                    'vnp_TransactionNo' => $payment->vnp_TransactionNo,
                    'vnp_ResponseCode' => $payment->vnp_ResponseCode,
                    'vnp_PayDate' => $payment->vnp_PayDate,
                    'is_success' => $payment->isSuccess(),
                    'is_pending' => $payment->isPending(),
                    'is_failed' => $payment->isFailed(),
                    'bill' => $payment->bill,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch'
            ], 404);
        }
    }

    /**
     * Get payment history for a bill
     */
    public function getPaymentHistory(Request $request, $billId): JsonResponse
    {
        try {
            $bill = Bill::findOrFail($billId);
            $payments = $bill->payments()->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bill_id' => $bill->id,
                    'bill_status' => $bill->status,
                    'total_amount' => $bill->total_amount,
                    'payments' => $payments->map(function ($payment) {
                        return [
                            'payment_id' => $payment->id,
                            'status' => $payment->status,
                            'amount' => $payment->vnp_Amount,
                            'vnp_TxnRef' => $payment->vnp_TxnRef,
                            'vnp_TransactionNo' => $payment->vnp_TransactionNo,
                            'vnp_ResponseCode' => $payment->vnp_ResponseCode,
                            'vnp_PayDate' => $payment->vnp_PayDate,
                            'vnp_BankCode' => $payment->vnp_BankCode,
                            'is_success' => $payment->isSuccess(),
                            'created_at' => $payment->created_at,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(Request $request, $paymentId): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            if ($payment->status !== Payment::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hủy giao dịch đang chờ xử lý'
                ], 400);
            }

            $payment->update(['status' => Payment::STATUS_CANCELLED]);

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy giao dịch thành công',
                'data' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch'
            ], 404);
        }
    }
}
