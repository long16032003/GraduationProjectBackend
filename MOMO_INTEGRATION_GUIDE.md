# Hướng dẫn tích hợp MoMo Payment

## 🔧 Backend API Endpoints

### 1. Tạo thanh toán MoMo
```
POST /momo/create-payment
```

**Request:**
```json
{
  "amount": 50000,
  "bill_id": "12345",
  "discount_amount": 5000,
  "orderInfo": "Thanh toán hóa đơn #12345",
  "redirectUrl": "http://localhost:3000/payment/momo/return",
  "userInfo": {
    "name": "Nguyễn Văn A",
    "email": "customer@example.com",
    "phoneNumber": "0123456789"
  }
}
```

**Response:**
```json
{
  "success": true,
  "payUrl": "https://test-payment.momo.vn/...",
  "orderId": "12345_1698765432_5678",
  "billId": "12345",
  "amount": 50000,
  "discountAmount": 5000,
  "message": "Tạo thanh toán thành công"
}
```

**⚠️ Lưu ý:** `orderId` được tự động tạo theo format `billId_timestamp_random` để đảm bảo tính duy nhất cho mỗi lần thanh toán, tránh lỗi "trùng orderId" từ MoMo.

### 2. Xử lý kết quả trả về (handleReturn)
```
GET /momo/return?partnerCode=MOMO&orderId=12345_1698765432_5678&...
```

**Response:**
```json
{
  "success": true,
  "message": "Thanh toán thành công",
  "orderId": "12345_1698765432_5678",
  "billId": "12345"
}
```

### 3. Kiểm tra trạng thái giao dịch
```
POST /momo/query-status
```

**Request:**
```json
{
  "orderId": "12345",
  "lang": "vi"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Truy vấn thành công",
  "data": {
    "orderId": "12345",
    "transId": "2234567890",
    "amount": 50000,
    "resultCode": 0,
    "message": "Giao dịch thành công",
    "payType": "web",
    "responseTime": 1634567890,
    "paymentOption": "momo",
    "extraData": "",
    "promotionInfo": null,
    "refundTrans": []
  }
}
```

## 🚀 Frontend Integration

### 1. Cập nhật component Checkout

```typescript
// Trong component Checkout.tsx
const handleSubmit = async (values: PaymentFormValues) => {
  if (values.payment_method === 'momo') {
    try {
      setIsProcessingPayment(true);
      
             const paymentData = {
         amount: total,
         bill_id: id,
         discount_amount: discountAmount || 0,
         orderInfo: `Thanh toán hóa đơn #${id}`,
         redirectUrl: `${window.location.origin}/payment/momo/return`,
         userInfo: {
           name: bill.customer_name || 'Khách hàng',
           email: bill.customer_email || '',
           phoneNumber: bill.customer_phone || ''
         }
       };

      const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';
      const result = await httpClient(`${API_URL}/momo/create-payment`, {
        method: 'POST',
        body: paymentData
      });

      if (result.success && result.payUrl) {
        message.success('Đang chuyển hướng đến MoMo...');
        // Redirect to MoMo payment page
        window.location.href = result.payUrl;
      } else {
        message.error(result.message || 'Có lỗi xảy ra khi tạo thanh toán MoMo');
      }
    } catch (error) {
      console.error('MoMo payment creation failed:', error);
      message.error('Không thể kết nối đến MoMo. Vui lòng thử lại.');
    } finally {
      setIsProcessingPayment(false);
    }
  }
  // ... other payment methods
};
```

### 2. Tạo component xử lý Return

```typescript
// Tạo file MoMoPaymentReturn.tsx
import React, { useEffect, useState } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import { Card, Result, Button, Spin, Typography, Alert } from 'antd';
import { CheckCircleOutlined, CloseCircleOutlined } from '@ant-design/icons';
import { httpClient } from '@/utils/http';

const { Title, Text } = Typography;

const MoMoPaymentReturn: React.FC = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [isProcessing, setIsProcessing] = useState(true);
  const [paymentResult, setPaymentResult] = useState<{
    success: boolean;
    message: string;
    orderId?: string;
    error?: string;
  } | null>(null);

  useEffect(() => {
    const processReturn = async () => {
      try {
        // Lấy tất cả parameters từ URL
        const params = {};
        const momoParams = [
          'partnerCode', 'orderId', 'requestId', 'amount', 'orderInfo',
          'orderType', 'transId', 'resultCode', 'message', 'payType',
          'responseTime', 'extraData', 'signature'
        ];

        momoParams.forEach(param => {
          const value = searchParams.get(param);
          if (value) {
            params[param] = value;
          }
        });

        // Gọi API handleReturn
        const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';
        const response = await httpClient(`${API_URL}/momo/return`, {
          method: 'GET',
          params: params
        });

                 setPaymentResult({
           success: response.success || false,
           message: response.message || 'Không có thông tin phản hồi',
           orderId: response.orderId || params.orderId,
           billId: response.billId || null,
           error: response.error
         });

      } catch (error) {
        console.error('Error processing MoMo return:', error);
        setPaymentResult({
          success: false,
          message: 'Có lỗi xảy ra khi xử lý kết quả thanh toán',
          error: error instanceof Error ? error.message : 'Unknown error'
        });
      } finally {
        setIsProcessingPayment(false);
      }
    };

    processReturn();
  }, [searchParams]);

     const handleGoBack = () => {
     if (paymentResult?.billId) {
       navigate(`/admin/bills/${paymentResult.billId}`);
     } else {
       navigate('/admin/bills');
     }
   };

  if (isProcessing) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Card className="w-full max-w-md">
          <div className="text-center">
            <Spin size="large" />
            <Title level={3} className="mt-4">Đang xử lý kết quả thanh toán</Title>
            <Text type="secondary">Vui lòng chờ trong giây lát...</Text>
          </div>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center p-4">
      <Card className="w-full max-w-2xl">
        {paymentResult?.success ? (
          <Result
            status="success"
            title="Thanh toán thành công!"
            subTitle={
                             <div className="space-y-2">
                 <p>{paymentResult.message}</p>
                 {paymentResult.billId && (
                   <p>
                     <Text strong>Mã hóa đơn: </Text>
                     <Text code>#{paymentResult.billId}</Text>
                   </p>
                 )}
                 {paymentResult.orderId && (
                   <p>
                     <Text strong>Mã giao dịch MoMo: </Text>
                     <Text code>{paymentResult.orderId}</Text>
                   </p>
                 )}
               </div>
            }
            extra={[
              <Button type="primary" key="view-bill" onClick={handleGoBack}>
                Xem hóa đơn
              </Button>
            ]}
          />
        ) : (
          <Result
            status="error"
            title="Thanh toán thất bại"
            subTitle={paymentResult?.message || 'Không thể xử lý thanh toán'}
            extra={[
              <Button type="primary" key="back" onClick={handleGoBack}>
                Quay lại
              </Button>
            ]}
          />
        )}
      </Card>
    </div>
  );
};

export default MoMoPaymentReturn;
```

### 3. Thêm route trong React Router

```typescript
// Trong file routes/index.tsx
import MoMoPaymentReturn from '@/components/MoMoPaymentReturn';

const routes = [
  // ... existing routes
  {
    path: '/payment/momo/return',
    element: <MoMoPaymentReturn />,
  },
];
```

### 4. Kiểm tra trạng thái giao dịch

```typescript
// Hàm kiểm tra trạng thái giao dịch
const checkMoMoPaymentStatus = async (orderId: string) => {
  try {
    const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';
    const response = await httpClient(`${API_URL}/momo/query-status`, {
      method: 'POST',
      body: {
        orderId: orderId,
        lang: 'vi'
      }
    });

    if (response.success) {
      console.log('Payment status:', response.data);
      return response.data;
    } else {
      console.error('Query failed:', response.message);
      return null;
    }
  } catch (error) {
    console.error('Error checking payment status:', error);
    return null;
  }
};

// Sử dụng trong component
const handleCheckStatus = async () => {
  const status = await checkMoMoPaymentStatus(orderId);
  if (status) {
    if (status.resultCode === 0) {
      message.success('Giao dịch đã thanh toán thành công!');
    } else {
      message.warning(`Trạng thái: ${status.message}`);
    }
  }
};
```

## 📝 Luồng hoạt động tổng thể

```mermaid
graph TD
    A[User click "Thanh toán MoMo"] --> B[Frontend gọi /momo/create-payment]
    B --> C[Backend tạo payment request]
    C --> D[Backend gọi MoMo API]
    D --> E[Nhận payUrl từ MoMo]
    E --> F[Redirect user đến MoMo]
    F --> G[User thanh toán trên MoMo]
    G --> H[MoMo redirect về /momo/return]
    H --> I[Backend xử lý handleReturn]
    I --> J[Frontend hiển thị kết quả]
    
    G --> K[MoMo gửi IPN đến /momo/notify]
    K --> L[Backend xử lý handleNotify]
    L --> M[Cập nhật database]
    
    J --> N[Có thể gọi /momo/query-status]
    N --> O[Kiểm tra trạng thái chính xác]
```

## 🎯 Các trường hợp sử dụng

### 1. Tạo thanh toán cơ bản
```typescript
const paymentData = {
  amount: 50000,
  bill_id: billId,
  discount_amount: 0,
  orderInfo: `Thanh toán hóa đơn #${billId}`
};
```

### 2. Thanh toán với giảm giá và thông tin khách hàng
```typescript
const paymentData = {
  amount: 50000,
  bill_id: billId,
  discount_amount: 5000,
  orderInfo: `Thanh toán hóa đơn #${billId}`,
  userInfo: {
    name: 'Nguyễn Văn A',
    email: 'customer@example.com',
    phoneNumber: '0123456789'
  }
};
```

### 3. Thanh toán với danh sách sản phẩm và giảm giá
```typescript
const paymentData = {
  amount: 50000,
  bill_id: billId,
  discount_amount: 10000,
  orderInfo: `Thanh toán hóa đơn #${billId}`,
  items: [
    {
      name: 'Món ăn 1',
      quantity: 2,
      amount: 25000,
      image: 'https://example.com/image1.jpg'
    },
    {
      name: 'Món ăn 2',
      quantity: 1,
      amount: 25000,
      image: 'https://example.com/image2.jpg'
    }
  ]
};
```

### 4. Thanh toán với mã giảm giá áp dụng
```typescript
// Ví dụ: Bill tổng tiền 100.000 VNĐ, giảm giá 15.000 VNĐ
const paymentData = {
  amount: 85000,        // Số tiền thực tế phải thanh toán
  bill_id: billId,
  discount_amount: 15000, // Số tiền đã được giảm
  orderInfo: `Thanh toán hóa đơn #${billId} (Đã giảm 15.000 VNĐ)`,
  userInfo: {
    name: 'Nguyễn Văn A',
    email: 'customer@example.com',
    phoneNumber: '0123456789'
  }
};
```

### 5. Định kỳ kiểm tra trạng thái
```typescript
const pollPaymentStatus = async (orderId: string, maxAttempts: number = 5) => {
  for (let i = 0; i < maxAttempts; i++) {
    const status = await checkMoMoPaymentStatus(orderId);
    
    if (status && status.resultCode === 0) {
      return { success: true, data: status };
    }
    
    // Đợi 5 giây trước khi kiểm tra lại
    await new Promise(resolve => setTimeout(resolve, 5000));
  }
  
  return { success: false, message: 'Timeout checking payment status' };
};
```

## 🔍 Các mã trạng thái thường gặp

| Mã | Ý nghĩa | Hành động |
|----|---------|-----------| 
| 0 | Giao dịch thành công | Cập nhật trạng thái paid |
| 1001 | Giao dịch đang chờ thanh toán | Tiếp tục polling |
| 1003 | Giao dịch đã bị hủy | Hiển thị thông báo hủy |
| 1004 | Giao dịch đã hết hạn | Tạo giao dịch mới |
| 2001 | Sai thông tin | Kiểm tra lại data |
| 21 | Số dư không đủ | Thông báo cho user |

## 🛠️ Debug và Testing

### 1. Log các request/response
```typescript
const debugMoMoPayment = (data: any, label: string) => {
  if (process.env.NODE_ENV === 'development') {
    console.log(`[MoMo ${label}]`, data);
  }
};
```

### 2. Test với số tiền nhỏ
```typescript
// Luôn test với số tiền >= 1000 VND
const testPayment = {
  amount: 1000, // Minimum amount
  orderId: `test_${Date.now()}`,
  orderInfo: 'Test payment'
};
```

### 3. Kiểm tra timeout
```typescript
// Set timeout 30s cho query API
const queryWithTimeout = async (orderId: string) => {
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 30000);
  
  try {
    const response = await fetch('/momo/query-status', {
      method: 'POST',
      signal: controller.signal,
      body: JSON.stringify({ orderId })
    });
    
    clearTimeout(timeoutId);
    return response;
  } catch (error) {
    if (error.name === 'AbortError') {
      throw new Error('Request timeout');
    }
    throw error;
  }
};
```

## 🔄 Xử lý lỗi "Trùng orderId"

Hệ thống đã được cập nhật để tránh lỗi "Yêu cầu bị từ chối vì trùng orderId":

### Giải pháp:
- **orderId tự động**: Hệ thống tự tạo orderId theo format `billId_timestamp_random`
- **Tham số đầu vào**: Sử ddụng `bill_id` thay vì `orderId` trong request
- **Response mới**: Trả về cả `orderId` (của MoMo) và `billId` (của hệ thống)

### Migration từ phiên bản cũ:
```typescript
// Cũ ❌
const paymentData = {
  orderId: billId,
  amount: total
};

// Mới ✅
const paymentData = {
  bill_id: billId, 
  amount: total
};
```

### Xử lý response:
```typescript
// Response sẽ có cả orderId và billId
if (result.success) {
  console.log('MoMo orderId:', result.orderId); // "123_1698765432_5678"
  console.log('Bill ID:', result.billId);       // "123"
  window.location.href = result.payUrl;
}
```

## 📋 Checklist triển khai

- [ ] Đã cấu hình đúng ACCESS_KEY và SECRET_KEY
- [ ] Đã thêm route cho các endpoint MoMo
- [ ] Đã tạo component xử lý return
- [ ] Đã test với số tiền hợp lệ (1000-50000000 VND)
- [ ] Đã xử lý các trường hợp lỗi
- [ ] Đã implement query status cho tracking
- [ ] Đã test flow hoàn chỉnh
- [ ] Đã cấu hình redirect URL đúng
- [ ] Đã handle timeout cho các API call
- [ ] ✅ Đã cập nhật sử dụng `bill_id` thay vì `orderId`
- [ ] ✅ Đã xử lý response với cả `orderId` và `billId` 
