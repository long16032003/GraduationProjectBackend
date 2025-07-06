# Hướng dẫn thiết lập VNPay cho hệ thống thanh toán

## 1. Cấu hình Environment

Thêm các dòng sau vào file `.env`:

```env
# VNPay Configuration
# Test environment (sandbox)
VNPAY_TMN_CODE=DEMO
VNPAY_HASH_SECRET=QWERTYUIOPASDFGHJKLZXCVBNM123456
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_API_URL=https://sandbox.vnpayment.vn/merchant_webapi/api/transaction

# Production environment (thay thế bằng thông tin thực khi triển khai)
# VNPAY_TMN_CODE=your_real_tmn_code
# VNPAY_HASH_SECRET=your_real_hash_secret
# VNPAY_URL=https://vnpayment.vn/paymentv2/vpcpay.html
# VNPAY_API_URL=https://vnpayment.vn/merchant_webapi/api/transaction

# Callback URLs - Cập nhật domain thực tế
VNPAY_RETURN_URL=http://localhost:8000/vnpay/return
VNPAY_IPN_URL=http://localhost:8000/vnpay/ipn
```

## 2. API Endpoints

### Tạo thanh toán
- **POST** `/vnpay/create-payment`
- **Body**: `{ "bill_id": 1 }`
- **Response**: URL thanh toán VNPay

### Xử lý kết quả thanh toán
- **GET** `/vnpay/return` - Xử lý khi khách hàng quay lại từ VNPay
- **POST** `/vnpay/ipn` - Webhook từ VNPay (tự động)

### Quản lý thanh toán
- **GET** `/vnpay/payment/{payment_id}` - Xem trạng thái thanh toán
- **GET** `/vnpay/history/{bill_id}` - Lịch sử thanh toán của hóa đơn
- **POST** `/vnpay/cancel/{payment_id}` - Hủy thanh toán (chỉ thanh toán pending)

## 3. Cách sử dụng

### Frontend Integration

```javascript
// Tạo thanh toán
const createPayment = async (billId) => {
  try {
    const response = await fetch('/vnpay/create-payment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ bill_id: billId })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Redirect to VNPay payment page
      window.location.href = data.data.payment_url;
    } else {
      alert(data.message);
    }
  } catch (error) {
    console.error('Payment creation failed:', error);
  }
};

// Kiểm tra trạng thái thanh toán
const checkPaymentStatus = async (paymentId) => {
  try {
    const response = await fetch(`/vnpay/payment/${paymentId}`);
    const data = await response.json();
    
    if (data.success) {
      console.log('Payment status:', data.data.status);
      console.log('Is success:', data.data.is_success);
    }
  } catch (error) {
    console.error('Failed to check payment status:', error);
  }
};
```

## 4. Quy trình thanh toán

1. **Khách hàng** chọn thanh toán VNPay
2. **Hệ thống** tạo payment record và URL thanh toán
3. **Khách hàng** được redirect đến VNPay
4. **Khách hàng** thực hiện thanh toán trên VNPay
5. **VNPay** gửi kết quả về hệ thống qua Return URL và IPN
6. **Hệ thống** cập nhật trạng thái thanh toán và hóa đơn

## 5. Trạng thái thanh toán

- `pending`: Đang chờ thanh toán
- `success`: Thanh toán thành công
- `failed`: Thanh toán thất bại
- `cancelled`: Đã hủy thanh toán

## 6. Mã lỗi VNPay phổ biến

- `00`: Giao dịch thành công
- `07`: Trừ tiền thành công nhưng giao dịch nghi ngờ
- `09`: Thẻ chưa đăng ký InternetBanking
- `10`: Xác thực sai quá 3 lần
- `11`: Đã hết hạn thanh toán
- `12`: Thẻ bị khóa
- `24`: Khách hàng hủy giao dịch
- `51`: Tài khoản không đủ số dư
- `99`: Lỗi khác

## 7. Bảo mật

- Tất cả dữ liệu được mã hóa bằng HMAC SHA512
- Xác thực chữ ký số từ VNPay
- Kiểm tra tính toàn vẹn số tiền
- Log đầy đủ các giao dịch

## 8. Testing

Với môi trường sandbox, có thể sử dụng:
- **TMN Code**: DEMO
- **Hash Secret**: QWERTYUIOPASDFGHJKLZXCVBNM123456
- Các thẻ test được VNPay cung cấp

## 9. Monitoring

Tất cả giao dịch được log trong Laravel log files:
- Tạo thanh toán: `storage/logs/laravel.log`
- Xử lý IPN: `storage/logs/laravel.log`
- Lỗi: `storage/logs/laravel.log`

## 10. Troubleshooting

### Lỗi chữ ký không hợp lệ
- Kiểm tra VNPAY_HASH_SECRET
- Đảm bảo không có ký tự đặc biệt trong URL

### Callback không hoạt động
- Kiểm tra VNPAY_RETURN_URL và VNPAY_IPN_URL
- Đảm bảo URL accessible từ internet (cho production)

### Giao dịch không cập nhật
- Kiểm tra logs
- Verify IPN endpoint hoạt động
- Kiểm tra database connection 
