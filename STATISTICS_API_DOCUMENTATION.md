# API DOCUMENTATION - THỐNG KÊ DOANH THU

## Tổng quan
API thống kê doanh thu cung cấp các endpoint để xem và phân tích doanh thu nhà hàng theo nhiều tiêu chí khác nhau.

## Base URL
```
GET /statistics/
```

## Authentication
Tất cả các endpoint đều yêu cầu authentication và quyền truy cập thống kê (role: manager hoặc admin).

### Headers Required:
```
Authorization: Bearer {access_token}
Content-Type: application/json
Accept: application/json
```

---

## 1. DASHBOARD STATISTICS

### GET `/statistics/dashboard`

Lấy thống kê tổng quan nhanh cho dashboard.

#### Request Parameters
Không có parameters

#### Response Success (200)
```json
{
  "status": "success",
  "data": {
    "today_revenue": 1250000,
    "month_revenue": 15000000,
    "year_revenue": 180000000,
    "today_orders": 25
  }
}
```

#### Response Fields
| Field | Type | Description |
|-------|------|-------------|
| today_revenue | float | Doanh thu hôm nay |
| month_revenue | float | Doanh thu tháng hiện tại |
| year_revenue | float | Doanh thu năm hiện tại |
| today_orders | integer | Số đơn hàng hôm nay |

#### Example cURL
```bash
curl -X GET "http://localhost:8000/statistics/dashboard" \
  -H "Authorization: Bearer your_token_here" \
  -H "Accept: application/json"
```

---

## 2. REVENUE STATISTICS

### GET `/statistics/revenue`

Lấy thống kê doanh thu chi tiết với các bộ lọc.

#### Request Parameters (Query String)

| Parameter | Type | Required | Description | Valid Values |
|-----------|------|----------|-------------|--------------|
| type | string | Yes | Loại thống kê | `daily`, `monthly`, `yearly` |
| start_date | date | Yes | Ngày bắt đầu | YYYY-MM-DD |
| end_date | date | Yes | Ngày kết thúc | YYYY-MM-DD |
| payment_method | string | No | Phương thức thanh toán | `cash`, `card`, `both`, `all` |
| status | string | No | Trạng thái hóa đơn | `paid`, `unpaid`, `cancelled`, `all` |

#### Business Rules
- `start_date` phải nhỏ hơn hoặc bằng `end_date`
- `end_date` không được lớn hơn ngày hiện tại
- Khoảng thời gian tối đa là 1 năm
- Mặc định `payment_method = all` và `status = paid`

#### Response Success (200)
```json
{
  "status": "success",
  "message": "Lấy thống kê thành công",
  "data": {
    "summary": {
      "total_revenue": 15000000,
      "total_orders": 120,
      "average_order_value": 125000,
      "period": "2024-01-01 to 2024-01-31"
    },
    "chart_data": [
      {
        "date": "2024-01-01",
        "revenue": 450000,
        "orders": 8,
        "label": "01/01/2024"
      },
      {
        "date": "2024-01-02",
        "revenue": 520000,
        "orders": 12,
        "label": "02/01/2024"
      }
    ],
    "breakdown": {
      "by_payment_method": {
        "cash": {
          "revenue": 8000000,
          "orders": 65
        },
        "card": {
          "revenue": 7000000,
          "orders": 55
        },
        "both": {
          "revenue": 0,
          "orders": 0
        }
      },
      "by_day_of_week": {
        "Thứ 2": {
          "revenue": 2000000,
          "orders": 18
        },
        "Thứ 3": {
          "revenue": 1800000,
          "orders": 15
        },
        "Thứ 4": {
          "revenue": 2200000,
          "orders": 20
        },
        "Thứ 5": {
          "revenue": 2500000,
          "orders": 22
        },
        "Thứ 6": {
          "revenue": 3000000,
          "orders": 25
        },
        "Thứ 7": {
          "revenue": 2800000,
          "orders": 24
        },
        "Chủ nhật": {
          "revenue": 2700000,
          "orders": 23
        }
      }
    }
  }
}
```

#### Example Requests

**1. Thống kê theo ngày:**
```bash
curl -X GET "http://localhost:8000/statistics/revenue?type=daily&start_date=2024-01-01&end_date=2024-01-31" \
  -H "Authorization: Bearer your_token_here" \
  -H "Accept: application/json"
```

**2. Thống kê theo tháng:**
```bash
curl -X GET "http://localhost:8000/statistics/revenue?type=monthly&start_date=2024-01-01&end_date=2024-12-31" \
  -H "Authorization: Bearer your_token_here" \
  -H "Accept: application/json"
```

**3. Thống kê với filter phương thức thanh toán:**
```bash
curl -X GET "http://localhost:8000/statistics/revenue?type=daily&start_date=2024-01-01&end_date=2024-01-31&payment_method=cash" \
  -H "Authorization: Bearer your_token_here" \
  -H "Accept: application/json"
```

---

## ERROR RESPONSES

### 401 Unauthorized
```json
{
  "status": "error",
  "message": "Bạn cần đăng nhập để truy cập chức năng này"
}
```

### 403 Forbidden
```json
{
  "status": "error",
  "message": "Bạn không có quyền truy cập chức năng thống kê"
}
```

### 422 Validation Error
```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "errors": {
    "start_date": [
      "Ngày bắt đầu là bắt buộc"
    ],
    "type": [
      "Loại thống kê phải là daily, monthly hoặc yearly"
    ]
  }
}
```

### 500 Internal Server Error
```json
{
  "status": "error",
  "message": "Lỗi hệ thống, vui lòng thử lại sau",
  "data": null
}
```

---

## DATA FORMATS

### Chart Data Format
Dữ liệu biểu đồ được format khác nhau tùy theo loại thống kê:

**Daily Chart Data:**
```json
{
  "date": "2024-01-01",
  "revenue": 450000,
  "orders": 8,
  "label": "01/01/2024"
}
```

**Monthly Chart Data:**
```json
{
  "date": "2024-01",
  "revenue": 15000000,
  "orders": 120,
  "label": "01/2024"
}
```

**Yearly Chart Data:**
```json
{
  "date": "2024",
  "revenue": 180000000,
  "orders": 1440,
  "label": "2024"
}
```

---

## INTEGRATION EXAMPLES

### JavaScript (Fetch API)
```javascript
// Lấy thống kê dashboard
async function getDashboardStats() {
  try {
    const response = await fetch('/statistics/dashboard', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${accessToken}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    console.log(data);
  } catch (error) {
    console.error('Error:', error);
  }
}

// Lấy thống kê doanh thu chi tiết
async function getRevenueStats(params) {
  const queryString = new URLSearchParams(params).toString();
  
  try {
    const response = await fetch(`/statistics/revenue?${queryString}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${accessToken}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Sử dụng
const statsParams = {
  type: 'daily',
  start_date: '2024-01-01',
  end_date: '2024-01-31',
  payment_method: 'cash'
};

getRevenueStats(statsParams).then(data => {
  console.log(data);
});
```

### PHP (Laravel HTTP Client)
```php
use Illuminate\Support\Facades\Http;

// Lấy thống kê dashboard
$response = Http::withToken($accessToken)
    ->get('/statistics/dashboard');

if ($response->successful()) {
    $dashboardData = $response->json();
    // Process data
}

// Lấy thống kê doanh thu chi tiết
$response = Http::withToken($accessToken)
    ->get('/statistics/revenue', [
        'type' => 'daily',
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-31',
        'payment_method' => 'cash'
    ]);

if ($response->successful()) {
    $revenueData = $response->json();
    // Process data
}
```

---

## TESTING

### Test Cases

**1. Test Dashboard API:**
```bash
# Valid request
curl -X GET "http://localhost:8000/statistics/dashboard" \
  -H "Authorization: Bearer valid_token" \
  -H "Accept: application/json"

# Expected: 200 OK with dashboard data
```

**2. Test Revenue API with valid parameters:**
```bash
curl -X GET "http://localhost:8000/statistics/revenue?type=daily&start_date=2024-01-01&end_date=2024-01-31" \
  -H "Authorization: Bearer valid_token" \
  -H "Accept: application/json"

# Expected: 200 OK with revenue statistics
```

**3. Test unauthorized access:**
```bash
curl -X GET "http://localhost:8000/statistics/dashboard" \
  -H "Accept: application/json"

# Expected: 401 Unauthorized
```

**4. Test invalid date range:**
```bash
curl -X GET "http://localhost:8000/statistics/revenue?type=daily&start_date=2024-01-31&end_date=2024-01-01" \
  -H "Authorization: Bearer valid_token" \
  -H "Accept: application/json"

# Expected: 422 Validation Error
```

---

## NOTES

1. **Performance**: API được tối ưu để xử lý trong vòng 5 giây
2. **Caching**: Có thể implement caching cho dashboard statistics
3. **Rate Limiting**: Có thể thêm rate limiting để tránh abuse
4. **Logging**: Tất cả requests đều được log để audit
5. **Database**: Sử dụng index trên trường `created_at` và `status` của bảng `bills`

## CHANGELOG

- **v1.0.0**: Initial release với dashboard và revenue statistics
- Có thể mở rộng thêm các loại thống kê khác như theo sản phẩm, theo bàn, v.v. 
