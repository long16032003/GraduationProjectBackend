# Hướng dẫn nhanh: Chạy Laravel Broadcasting với Socket.io

## 🚀 Bước cài đặt nhanh

### 1. Cài đặt dependencies

```bash
# Backend packages
composer require pusher/pusher-php-server

# Frontend packages (đã cài sẵn trong package.json)
npm install
```

### 2. Cấu hình .env

Thêm vào file `.env`:

```bash
# Broadcasting
BROADCAST_CONNECTION=redis
QUEUE_CONNECTION=redis

# Redis (cần có Redis server)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# App environment
APP_ENV=local
```

### 3. Cài đặt Laravel Echo Server

```bash
# Cài đặt globally
npm install -g laravel-echo-server

# Khởi tạo config
laravel-echo-server init
```

Khi được hỏi, chọn:
- Use default auth host: Yes
- Database: Redis
- Port: 6001
- Dev mode: Yes

### 4. Tạo channels authorization

Tạo file `routes/channels.php` (nếu chưa có):

```php
<?php

use Illuminate\Support\Facades\Broadcast;

// Private channel cho admin notifications
Broadcast::channel('admin-notifications', function ($user) {
    return $user !== null; // Đơn giản cho test
});

// Private channel cho specific user
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

## 🏃‍♂️ Chạy thử nghiệm

### 1. Khởi động services

Mở 4 terminal và chạy các lệnh sau:

```bash
# Terminal 1: Redis server
redis-server

# Terminal 2: Laravel queue worker
php artisan queue:work --sleep=3 --tries=3

# Terminal 3: Laravel Echo Server
laravel-echo-server start

# Terminal 4: Laravel development server
php artisan serve
```

### 2. Build frontend assets

```bash
# Terminal 5
npm run dev
```

### 3. Test broadcasting

Mở browser và vào Developer Console, sau đó test:

```javascript
// Test 1: Basic connection
console.log('Echo available:', !!window.Echo);

// Test 2: Listen to test channel
Echo.channel('restaurant-orders')
    .listen('.new.order', (data) => {
        console.log('New order received:', data);
        alert('Đơn hàng mới: ' + data.customer_name);
    });

// Test 3: Send test broadcast
window.testBroadcast();
```

### 4. Kiểm tra trong browser

1. Mở `http://localhost:8000`
2. Mở Developer Console
3. Chạy: `window.testBroadcast()`
4. Bạn sẽ thấy notification xuất hiện ở góc phải màn hình

## 🔧 Troubleshooting nhanh

### Lỗi thường gặp:

1. **Redis connection failed**
   ```bash
   # Kiểm tra Redis có chạy không
   redis-cli ping
   # Nếu lỗi, cài đặt Redis:
   # Windows: https://github.com/microsoftarchive/redis/releases
   # Mac: brew install redis
   # Ubuntu: sudo apt install redis-server
   ```

2. **Laravel Echo Server không connect**
   ```bash
   # Kiểm tra port 6001 có bị chiếm không
   netstat -an | findstr :6001
   # Thử port khác trong laravel-echo-server.json
   ```

3. **Queue jobs không chạy**
   ```bash
   # Kiểm tra queue jobs
   php artisan queue:failed
   # Clear failed jobs
   php artisan queue:flush
   ```

4. **Frontend lỗi Echo undefined**
   ```bash
   # Rebuild assets
   npm run build
   # Hoặc
   npm run dev
   ```

## 📱 Demo nhanh

Sau khi setup xong, bạn có thể:

1. **Test đơn hàng mới**: Gọi API tạo order và xem notification realtime
2. **Test cập nhật bàn**: Call API cập nhật table status
3. **Monitor trong console**: Xem logs realtime events

### Ví dụ test API:

```bash
# Test tạo đơn hàng (cần authentication)
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"table_id": 1, "bill_id": 1, "order_dishes": [{"dish_id": 1, "quantity": 2, "price_at_order_time": 50000}]}'

# Test broadcast trực tiếp (chỉ trong development)
curl -X POST http://localhost:8000/api/test-broadcast
```

## 🎯 Next Steps

Sau khi test thành công:

1. Tích hợp vào UI thực của bạn
2. Thêm authentication cho private channels
3. Customize notifications UI
4. Add more events (reservations, payments, etc.)
5. Setup production environment

## 📞 Support

Nếu gặp vấn đề:
1. Kiểm tra logs trong `storage/logs/laravel.log`
2. Check browser console errors
3. Verify Redis và Queue worker đang chạy
4. Test từng bước một để isolate issue

---

**Thời gian setup**: ~10-15 phút
**Prerequisites**: Redis server, Node.js, PHP, Composer 
