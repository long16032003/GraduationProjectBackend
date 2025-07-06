<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đặt bàn</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .reservation-details {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 25px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #34495e;
            min-width: 140px;
        }
        .detail-value {
            color: #2c3e50;
            font-weight: 500;
        }
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .notes {
            background-color: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .notes h4 {
            margin: 0 0 10px 0;
            color: #0c5460;
        }
        .contact-info {
            background-color: #fff8e1;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .contact-info h4 {
            margin: 0 0 15px 0;
            color: #f57f17;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 40px;
        }
        .footer p {
            margin: 5px 0;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
            .detail-label {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">{{ config('restaurant.name', $restaurantName) }}</div>
            <h1>🍽️ Xác nhận đặt bàn</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Xin chào <strong>{{ $reservation->name }}</strong>! 👋
            </div>

            <p>Cảm ơn bạn đã đặt bàn tại nhà hàng chúng tôi. Dưới đây là thông tin chi tiết về đặt bàn của bạn:</p>

            <!-- Reservation Details -->
            <div class="reservation-details">
                <div class="detail-row">
                    <span class="detail-label">📅 Ngày đặt:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">🕒 Thời gian:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">👥 Số khách:</span>
                    <span class="detail-value">{{ $reservation->number_of_guests }} người</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">🏷️ Tên khách hàng:</span>
                    <span class="detail-value">{{ $reservation->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📞 Số điện thoại:</span>
                    <span class="detail-value">{{ $reservation->phone }}</span>
                </div>
                @if($reservation->table)
                <div class="detail-row">
                    <span class="detail-label">🪑 Bàn số:</span>
                    <span class="detail-value">{{ $reservation->table->name ?? 'Bàn #' . $reservation->table_id }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">📋 Trạng thái:</span>
                    <span class="detail-value">
                        <span class="status {{ $reservation->status }}">
                            @switch($reservation->status)
                                @case('pending')
                                    Chờ xác nhận
                                    @break
                                @case('confirmed')
                                    Đã xác nhận
                                    @break
                                @case('cancelled')
                                    Đã hủy
                                    @break
                                @default
                                    {{ $reservation->status }}
                            @endswitch
                        </span>
                    </span>
                </div>
            </div>

            @if($reservation->notes)
            <div class="notes">
                <h4>📝 Ghi chú:</h4>
                <p>{{ $reservation->notes }}</p>
            </div>
            @endif

            @if($reservation->status === 'pending')
            <div class="contact-info">
                <h4>⏰ Lưu ý quan trọng</h4>
                <p>Đặt bàn của bạn đang chờ xác nhận. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận.</p>
            </div>
            @elseif($reservation->status === 'confirmed')
            <div class="contact-info">
                <h4>✅ Đặt bàn đã được xác nhận!</h4>
                <p>Chúng tôi rất mong được phục vụ bạn. Vui lòng đến đúng giờ để có trải nghiệm tốt nhất.</p>
            </div>
            @endif

            <div class="contact-info">
                <h4>📞 Cần hỗ trợ?</h4>
                <p>Nếu bạn cần thay đổi hoặc hủy đặt bàn, vui lòng liên hệ với chúng tôi:</p>
                <p><strong>Hotline:</strong> {{ config('restaurant.contact.phone') }}</p>
                <p><strong>Email:</strong> {{ config('restaurant.contact.email') }}</p>
            </div>

            <center>
                <a href="{{ config('app.url') }}" class="button">Xem thực đơn</a>
            </center>

            <p style="margin-top: 30px;">
                Cảm ơn bạn đã tin tưởng và lựa chọn {{ config('restaurant.name', $restaurantName) }}. Chúng tôi cam kết mang đến cho bạn những trải nghiệm ẩm thực tuyệt vời nhất! 🌟
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ config('restaurant.name', $restaurantName) }}</strong></p>
            <p>📍 {{ config('restaurant.contact.address') }}</p>
            <p>📞 {{ config('restaurant.contact.phone') }} | 📧 {{ config('restaurant.contact.email') }}</p>
            @if(config('restaurant.contact.website'))
                <p>🌐 <a href="{{ config('restaurant.contact.website') }}" style="color: #fff;">{{ config('restaurant.contact.website') }}</a></p>
            @endif
            <p style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
                © {{ date('Y') }} {{ config('restaurant.name', $restaurantName) }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
