# ĐẶTTẢ USECASE: THỐNG KÊ DOANH THU

## Thông tin chung

| Thuộc tính | Mô tả |
|------------|-------|
| **Tên usecase** | Thống kê doanh thu nhà hàng |
| **Mã usecase** | UC-STAT-001 |
| **Tác nhân chính** | Quản lý nhà hàng (Restaurant Manager) |
| **Tác nhân phụ** | Hệ thống cơ sở dữ liệu |
| **Mục đích** | Cho phép xem và phân tích doanh thu theo ngày, tháng, năm với các bộ lọc linh hoạt |
| **Mức độ ưu tiên** | Cao |
| **Phạm vi** | Hệ thống quản lý nhà hàng |
| **Mức độ phức tạp** | Trung bình |

## Điều kiện

### Điều kiện kích hoạt
- Tác nhân chọn chức năng "Thống kê" từ menu hệ thống quản lý
- Tác nhân truy cập URL endpoint thống kê

### Điều kiện tiên quyết
- Tác nhân đã đăng nhập thành công vào hệ thống
- Tác nhân có quyền truy cập module thống kê (role: manager)
- Hệ thống có dữ liệu hóa đơn trong cơ sở dữ liệu

### Điều kiện thành công
- Báo cáo doanh thu được hiển thị đầy đủ và chính xác
- Biểu đồ/tổng hợp được render thành công
- Dữ liệu được trình bày theo định dạng mong muốn

### Điều kiện thất bại
- Không có dữ liệu trong khoảng thời gian được chọn
- Truy cập trái phép - không đủ quyền hạn
- Lỗi kết nối cơ sở dữ liệu
- Tham số đầu vào không hợp lệ

## Luồng sự kiện

### Luồng sự kiện chính
1. **Khởi tạo**: Tác nhân chọn mục "Thống kê" từ menu chính
2. **Hiển thị giao diện**: Hệ thống hiển thị giao diện bộ lọc thống kê với các tùy chọn:
   - Loại thống kê: Doanh thu theo ngày/tháng/năm
   - Khoảng thời gian (từ ngày - đến ngày)
   - Phương thức thanh toán (cash/card/both)
   - Trạng thái hóa đơn (paid/unpaid/all)
3. **Nhập điều kiện**: Tác nhân nhập/chọn các điều kiện lọc mong muốn
4. **Gửi yêu cầu**: Tác nhân nhấn nút "Xem báo cáo"/"Tạo thống kê"
5. **Xử lý dữ liệu**: Hệ thống thực hiện:
   - Validate dữ liệu đầu vào
   - Truy xuất dữ liệu từ bảng bills theo điều kiện
   - Tính toán và tổng hợp dữ liệu
6. **Trả về kết quả**: Hệ thống hiển thị:
   - Tổng doanh thu trong khoảng thời gian
   - Biểu đồ doanh thu theo thời gian
   - Thống kê chi tiết theo từng tiêu chí
   - Khả năng xuất báo cáo (PDF/Excel)

### Luồng sự kiện thay thế

#### 3a. Sử dụng bộ lọc mặc định
- 3a1. Tác nhân không nhập điều kiện lọc cụ thể
- 3a2. Hệ thống sử dụng bộ lọc mặc định (30 ngày gần nhất)
- 3a3. Trở về bước 4 của luồng chính

#### 5a. Không có dữ liệu trong khoảng thời gian
- 5a1. Hệ thống kiểm tra và phát hiện không có dữ liệu
- 5a2. Hiển thị thông báo "Không có dữ liệu trong khoảng thời gian đã chọn"
- 5a3. Đề xuất mở rộng khoảng thời gian tìm kiếm
- 5a4. Trở về bước 2

### Luồng sự kiện ngoại lệ

#### E1. Lỗi xác thực quyền truy cập
- E1.1. Hệ thống phát hiện tác nhân không có quyền truy cập
- E1.2. Hiển thị thông báo lỗi "Bạn không có quyền truy cập chức năng này"
- E1.3. Chuyển hướng về trang chính hoặc đăng nhập

#### E2. Lỗi cơ sở dữ liệu
- E2.1. Hệ thống không thể kết nối hoặc truy vấn cơ sở dữ liệu
- E2.2. Hiển thị thông báo lỗi "Lỗi hệ thống, vui lòng thử lại sau"
- E2.3. Ghi log lỗi để admin xử lý

#### E3. Tham số đầu vào không hợp lệ
- E3.1. Hệ thống validate và phát hiện dữ liệu đầu vào sai định dạng
- E3.2. Hiển thị thông báo lỗi cụ thể cho từng trường
- E3.3. Yêu cầu tác nhân nhập lại

## Quy tắc nghiệp vụ

### BR-STAT-001: Quyền truy cập
- Chỉ các user có role "manager" hoặc "admin" mới được truy cập tính năng này

### BR-STAT-002: Phạm vi thời gian
- Khoảng thời gian tối đa cho một lần thống kê là 1 năm
- Ngày bắt đầu không được lớn hơn ngày kết thúc

### BR-STAT-003: Tính toán doanh thu
- Chỉ tính các hóa đơn có status = 'paid'
- Doanh thu = total_amount - discount_amount

### BR-STAT-004: Hiệu suất
- Response time không được vượt quá 5 giây
- Hỗ trợ phân trang nếu dữ liệu lớn

## Dữ liệu đầu vào

| Tham số | Loại | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| type | string | Có | Loại thống kê: 'daily', 'monthly', 'yearly' |
| start_date | date | Có | Ngày bắt đầu (YYYY-MM-DD) |
| end_date | date | Có | Ngày kết thúc (YYYY-MM-DD) |
| payment_method | string | Không | Phương thức thanh toán: 'cash', 'card', 'both' |
| status | string | Không | Trạng thái hóa đơn: 'paid', 'unpaid', 'all' |

## Dữ liệu đầu ra

### Định dạng JSON Response:
```json
{
  "status": "success",
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
        "orders": 8
      }
    ],
    "breakdown": {
      "by_payment_method": {
        "cash": 8000000,
        "card": 7000000
      },
      "by_day_of_week": {
        "monday": 2000000,
        "tuesday": 1800000
      }
    }
  }
}
```

## Giao diện người dùng

### Các thành phần UI cần thiết:
1. **Bộ lọc thống kê**:
   - Dropdown chọn loại thống kê
   - Date picker cho khoảng thời gian
   - Checkbox/Radio button cho các tùy chọn lọc

2. **Khu vực hiển thị kết quả**:
   - Cards hiển thị các chỉ số tổng quan
   - Biểu đồ dạng line/bar/pie chart
   - Bảng dữ liệu chi tiết

3. **Chức năng xuất báo cáo**:
   - Nút xuất PDF
   - Nút xuất Excel
   - Nút in báo cáo

## Yêu cầu phi chức năng

### Hiệu suất
- Thời gian phản hồi: < 5 giây
- Hỗ trợ tối đa 100 concurrent users

### Bảo mật
- Xác thực JWT token
- Phân quyền dựa trên role
- Log tất cả hoạt động truy cập

### Khả năng mở rộng
- Hỗ trợ thêm loại thống kê mới
- Dễ dàng tích hợp với các dashboard tools

## Test Cases

### TC-STAT-001: Thống kê doanh thu theo ngày - Happy path
- **Điều kiện đầu**: User đã đăng nhập với role manager
- **Dữ liệu vào**: type="daily", start_date="2024-01-01", end_date="2024-01-31"
- **Kết quả mong đợi**: Trả về dữ liệu thống kê đầy đủ với status 200

### TC-STAT-002: Không có quyền truy cập
- **Điều kiện đầu**: User đăng nhập với role staff
- **Dữ liệu vào**: Truy cập endpoint thống kê
- **Kết quả mong đợi**: Trả về lỗi 403 Forbidden

### TC-STAT-003: Khoảng thời gian không hợp lệ
- **Điều kiện đầu**: User đã đăng nhập với role manager
- **Dữ liệu vào**: start_date="2024-01-31", end_date="2024-01-01"
- **Kết quả mong đợi**: Trả về lỗi validation 422

## Tài liệu tham khảo
- Laravel Documentation
- Chart.js Documentation
- Restaurant Management System Requirements 
