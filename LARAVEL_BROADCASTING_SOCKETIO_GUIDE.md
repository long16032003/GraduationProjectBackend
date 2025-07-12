# Laravel Broadcasting với Socket.io - Hướng dẫn đầy đủ

## 📋 Tổng quan

Laravel Broadcasting là một tính năng mạnh mẽ cho phép bạn broadcast (phát sóng) các events từ server-side Laravel đến client-side thông qua WebSocket. Điều này cho phép tạo ra các ứng dụng realtime như:

- Thông báo tức thời
- Chat realtime  
- Cập nhật trạng thái live
- Notifications realtime
- Dashboard cập nhật theo thời gian thực

## 🛠️ Cài đặt và Cấu hình

### 1. Cài đặt packages cần thiết

```bash
# Backend Laravel packages
composer require pusher/pusher-php-server

# Frontend packages
npm install laravel-echo socket.io-client
```

### 2. Cấu hình Broadcasting Driver

Trong file `.env`, thêm các cấu hình sau:

```bash
# Broadcasting
BROADCAST_CONNECTION=redis
QUEUE_CONNECTION=redis

# Redis Configuration (cần thiết cho broadcasting)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Socket.io Server (Laravel Echo Server)
LARAVEL_ECHO_SERVER_HOST=localhost
LARAVEL_ECHO_SERVER_PORT=6001
```

### 3. Cấu hình Queues

Broadcasting hoạt động tốt nhất với queue system. Cấu hình queue trong `config/queue.php`:

```php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

### 4. Cài đặt Laravel Echo Server

```bash
# Cài đặt globally
npm install -g laravel-echo-server

# Hoặc thêm vào devDependencies
npm install --save-dev laravel-echo-server

# Khởi tạo cấu hình
laravel-echo-server init
```

Cấu hình file `laravel-echo-server.json`:

```json
{
    "authHost": "http://localhost:8000",
    "authEndpoint": "/broadcasting/auth",
    "clients": [
        {
            "appId": "your-app-id",
            "key": "your-app-key"
        }
    ],
    "database": "redis",
    "databaseConfig": {
        "redis": {
            "host": "127.0.0.1",
            "port": "6379"
        }
    },
    "devMode": true,
    "host": null,
    "port": "6001",
    "protocol": "http",
    "socketio": {},
    "sslCertPath": "",
    "sslKeyPath": ""
}
```

## 📡 Tạo Broadcasting Events

### 1. Tạo Event Class

```php
<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // Định nghĩa channels để broadcast
    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant-orders'),           // Public channel
            new PrivateChannel('admin-notifications'),  // Private channel
        ];
    }

    // Tên event broadcast
    public function broadcastAs(): string
    {
        return 'new.order';
    }

    // Dữ liệu được broadcast
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'table_id' => $this->order->table_id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'customer_name' => $this->order->customer_name,
            'dishes' => $this->order->orderDishes->map(function ($orderDish) {
                return [
                    'dish_name' => $orderDish->dish->name,
                    'quantity' => $orderDish->quantity,
                    'price' => $orderDish->price,
                ];
            }),
            'created_at' => $this->order->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
```

### 2. Trigger Events trong Controller

```php
use App\Events\NewOrderEvent;

class StoreOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // ... logic tạo order ...
        
        $order = Order::create($dataCreate);
        
        if($order) {
            // ... tạo order dishes ...
            
            // Load relationships cho broadcasting
            $order->load(['orderDishes.dish', 'table']);
            
            // Broadcast event
            NewOrderEvent::dispatch($order);
        }
        
        return response()->json($order, 201);
    }
}
```

## 🌐 Frontend Setup (JavaScript)

### 1. Cấu hình Laravel Echo

```javascript
// resources/js/echo.js
import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
        },
    },
});

export default window.Echo;
```

### 2. Lắng nghe Events

```javascript
// resources/js/realtime-notifications.js
import Echo from './echo.js';

class RestaurantRealtimeNotifications {
    constructor() {
        this.initializeOrderNotifications();
        this.initializeTableStatusUpdates();
    }

    initializeOrderNotifications() {
        // Lắng nghe public channel
        Echo.channel('restaurant-orders')
            .listen('.new.order', (data) => {
                console.log('New order received:', data);
                this.showNotification(
                    'Đơn hàng mới!',
                    `Bàn ${data.table_id} - ${data.customer_name}`,
                    'success'
                );
                this.updateOrderList(data);
            });

        // Lắng nghe private channel (cần authentication)
        Echo.private('admin-notifications')
            .listen('.new.order', (data) => {
                this.showNotification(
                    'Thông báo Admin',
                    'Có đơn hàng mới cần xử lý',
                    'warning'
                );
            });
    }

    showNotification(title, message, type) {
        // Hiển thị notification UI
        // Implement theo design của bạn
    }
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    window.restaurantNotifications = new RestaurantRealtimeNotifications();
});
```

## 🔐 Authentication cho Private Channels

### 1. Định nghĩa authorization routes

```php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin-notifications', function ($user) {
    return $user && $user->hasRole('admin');
});

Broadcast::channel('user-{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

### 2. Middleware authentication

```php
// config/broadcasting.php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        // ... other config
        'options' => [
            'auth_endpoint' => '/broadcasting/auth',
        ],
    ],
],
```

## 🏃‍♂️ Chạy hệ thống Realtime

### 1. Khởi động các services cần thiết

```bash
# 1. Khởi động Redis server
redis-server

# 2. Khởi động Laravel queue worker
php artisan queue:work --sleep=3 --tries=3

# 3. Khởi động Laravel Echo Server
laravel-echo-server start

# 4. Khởi động Laravel development server
php artisan serve

# 5. Build frontend assets
npm run dev
```

### 2. Kiểm tra kết nối

Mở Developer Tools trong browser và chạy:

```javascript
// Test basic connection
console.log(window.Echo);

// Test listening to a channel
Echo.channel('test-channel')
    .listen('.test-event', (data) => {
        console.log('Received:', data);
    });
```

## 📊 Ví dụ thực tiễn cho Nhà hàng

### 1. Thông báo đơn hàng mới

```php
// Event: NewOrderEvent
class NewOrderEvent implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant-orders'),
            new PrivateChannel('kitchen-orders'),
        ];
    }
}

// Trigger trong OrderController
NewOrderEvent::dispatch($order);
```

### 2. Cập nhật trạng thái bàn ăn

```php
// Event: TableStatusUpdated
class TableStatusUpdated implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [new Channel('restaurant-tables')];
    }
}

// Trigger khi cập nhật table
TableStatusUpdated::dispatch($table);
```

### 3. Thông báo đặt bàn mới

```php
// Event: ReservationCreated
class ReservationCreated implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant-reservations'),
            new PrivateChannel('manager-notifications'),
        ];
    }
}
```

## 🎛️ Dashboard Realtime

```javascript
class RealtimeDashboard {
    constructor() {
        this.initializeChannels();
    }

    initializeChannels() {
        // Thống kê realtime
        Echo.channel('restaurant-stats')
            .listen('.stats.updated', (data) => {
                this.updateStatsDisplay(data);
            });

        // Đơn hàng mới
        Echo.channel('restaurant-orders')
            .listen('.new.order', (data) => {
                this.incrementOrderCount();
                this.updateRevenueDisplay(data.total_amount);
            });

        // Cập nhật bàn ăn
        Echo.channel('restaurant-tables')
            .listen('.table.status.updated', (data) => {
                this.updateTableDisplay(data);
            });
    }

    updateStatsDisplay(data) {
        document.getElementById('today-orders').textContent = data.today_orders;
        document.getElementById('total-revenue').textContent = data.total_revenue;
        document.getElementById('active-tables').textContent = data.active_tables;
    }
}
```

## 🔧 Debugging và Troubleshooting

### 1. Kiểm tra Broadcasting connections

```bash
# Kiểm tra Redis connection
redis-cli ping

# Kiểm tra queue jobs
php artisan queue:failed

# Debug broadcast events
php artisan tinker
>>> broadcast(new App\Events\NewOrderEvent($order));
```

### 2. Logging events

```php
// Trong Event class
public function broadcastWith(): array
{
    \Log::info('Broadcasting NewOrderEvent', ['order_id' => $this->order->id]);
    
    return [
        'order_id' => $this->order->id,
        // ... other data
    ];
}
```

### 3. Frontend debugging

```javascript
// Enable debug mode
window.Echo.connector.socket.on('connect', () => {
    console.log('Socket.io connected');
});

window.Echo.connector.socket.on('disconnect', () => {
    console.log('Socket.io disconnected');
});

// Listen to all events on a channel
Echo.channel('restaurant-orders')
    .listenToAll((eventName, data) => {
        console.log(`Event: ${eventName}`, data);
    });
```

## 🚀 Production Deployment

### 1. Environment variables

```bash
# Production .env
BROADCAST_CONNECTION=redis
QUEUE_CONNECTION=redis
REDIS_HOST=your-redis-host
LARAVEL_ECHO_SERVER_PORT=6001
```

### 2. Process management với Supervisor

```ini
# /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work redis --sleep=3 --tries=3
directory=/path/to/your/app
autostart=true
autorestart=true
numprocs=3
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log

# Laravel Echo Server
[program:laravel-echo-server]
command=laravel-echo-server start
directory=/path/to/your/app
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/echo-server.log
```

### 3. SSL Configuration

```json
// laravel-echo-server.json cho production
{
    "authHost": "https://your-domain.com",
    "host": "your-domain.com",
    "port": "6001",
    "protocol": "https",
    "sslCertPath": "/path/to/cert.pem",
    "sslKeyPath": "/path/to/private-key.pem"
}
```

## 📈 Performance Optimization

### 1. Queue Optimization

```php
// Trong Event class
class NewOrderEvent implements ShouldBroadcastNow // Broadcast ngay lập tức
{
    // Hoặc implement ShouldBroadcast để đưa vào queue
}
```

### 2. Channel Optimization

```php
// Chỉ broadcast đến specific users
public function broadcastOn(): array
{
    return [
        new PrivateChannel('user.' . $this->order->creator_id),
        new PrivateChannel('table.' . $this->order->table_id),
    ];
}
```

### 3. Data Optimization

```php
public function broadcastWith(): array
{
    // Chỉ gửi dữ liệu cần thiết
    return [
        'id' => $this->order->id,
        'status' => $this->order->status,
        'table_id' => $this->order->table_id,
        // Không gửi toàn bộ object
    ];
}
```

## 🧪 Testing

### 1. Feature Tests

```php
// tests/Feature/BroadcastingTest.php
use Illuminate\Support\Facades\Event;

class BroadcastingTest extends TestCase
{
    public function test_new_order_broadcasts_event()
    {
        Event::fake();
        
        $response = $this->postJson('/api/orders', $orderData);
        
        Event::assertDispatched(NewOrderEvent::class);
    }
}
```

### 2. Manual Testing

```bash
# Test route đã tạo sẵn (chỉ trong development)
curl -X POST http://localhost:8000/api/test-broadcast
curl -X POST http://localhost:8000/api/test-broadcast/table-status
```

## 🎯 Best Practices

1. **Sử dụng Queues**: Luôn sử dụng queue cho broadcasting để không block request
2. **Optimize Data**: Chỉ broadcast dữ liệu cần thiết
3. **Error Handling**: Implement proper error handling cho realtime events
4. **Authentication**: Sử dụng private channels cho dữ liệu nhạy cảm
5. **Monitoring**: Monitor queue jobs và broadcasting performance
6. **Graceful Degradation**: Ứng dụng vẫn hoạt động khi broadcasting fails

## 📚 Tài liệu tham khảo

- [Laravel Broadcasting Documentation](https://laravel.com/docs/broadcasting)
- [Laravel Echo Documentation](https://laravel.com/docs/broadcasting#client-side-installation)
- [Socket.io Documentation](https://socket.io/docs/)
- [Laravel Echo Server](https://github.com/tlaverdure/laravel-echo-server)

---

**Lưu ý**: Hướng dẫn này được thiết kế đặc biệt cho hệ thống quản lý nhà hàng với các tính năng realtime như thông báo đơn hàng mới, cập nhật trạng thái bàn ăn, và dashboard theo thời gian thực. 
