<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Mail\ReservationConfirmationMail;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class StoreReservationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'phone' => 'required|string',
            'name' => 'required|string',
            'reservation_date' => 'required|integer',
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'notes' => 'nullable|string',
            'number_of_guests' => 'required|integer',
        ]);

        try {
            $data = $validated;

            // Convert Unix timestamp to MySQL datetime format
            $timestamp = (int) $data['reservation_date'];
            $data['reservation_date'] = Carbon::createFromTimestamp($timestamp, 'Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
            $data['status'] = 'pending';

            $user = $request->user();

            if ($user instanceof User) {
                $data['creator_id'] = $user->id;
                $data['creator_type'] = 'staff';
            }

//             If authenticated as customer, set customer_id as creator
            if ($user instanceof Customer) {
                $data['customer_id'] = $user->id;
                $data['creator_id'] = $user->id;
                $data['creator_type'] = 'customer';
            }

            $reservation = Reservation::create($data);

            // Load relationships để có đầy đủ thông tin cho email
            $reservation->load(['table', 'customer']);

            if ($user instanceof Customer && $reservation) {
                // Gửi email xác nhận đặt bàn
                $this->sendReservationConfirmationEmail($reservation);
            }
            return new JsonResponse([
                'success' => true,
                'message' => 'Đặt bàn thành công',
                'data' => $reservation
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đặt bàn thất bại',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gửi email xác nhận đặt bàn
     */
    private function sendReservationConfirmationEmail(Reservation $reservation)
    {
        try {
            $emailAddress = null;
            $customer = null;

            // Nếu có customer_id, lấy email từ customer
            if ($reservation->customer_id && $reservation->customer) {
                $customer = $reservation->customer;
                $emailAddress = $customer->email;
            }

            // Nếu không có customer hoặc customer không có email,
            // có thể thêm logic khác ở đây (ví dụ: lấy email từ form input)

            // Chỉ gửi email nếu có địa chỉ email hợp lệ
            if ($emailAddress && filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                Mail::to($emailAddress)->send(new ReservationConfirmationMail($reservation, $customer));

                Log::info('Reservation confirmation email sent', [
                    'reservation_id' => $reservation->id,
                    'email' => $emailAddress,
                    'customer_name' => $reservation->name
                ]);
            } else {
                Log::warning('Could not send reservation confirmation email - no valid email', [
                    'reservation_id' => $reservation->id,
                    'customer_id' => $reservation->customer_id,
                    'customer_name' => $reservation->name
                ]);
            }
        } catch (\Exception $e) {
            // Log lỗi nhưng không làm fail request đặt bàn
            Log::error('Failed to send reservation confirmation email', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
