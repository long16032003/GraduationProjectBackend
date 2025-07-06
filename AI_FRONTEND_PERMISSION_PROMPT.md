# 🤖 PROMPT CHO AI FRONTEND - Hệ thống phân quyền

## **CONTEXT: Role-Based Permission System**

Bạn đang làm việc với một hệ thống quản lý nhà hàng có **hệ thống phân quyền RBAC (Role-Based Access Control)**. Dưới đây là thông tin chi tiết về cách tích hợp phân quyền vào frontend:

---

## **1. 🎯 CẤU TRÚC HỆ THỐNG PHÂN QUYỀN**

### **Permission Format:**
- **Format**: `"resource:action"` 
- **Examples**: `"user:create"`, `"dish:browse"`, `"bill:update"`

### **User Permission Object:**
```typescript
interface User {
  id: number;
  name: string;
  email: string;
  superadmin: boolean;  // true = có tất cả quyền
  roles: Role[];
  permissions: {
    [key: string]: 1;  // 1 = có quyền, không có key = không có quyền
  };
}

// Example:
const user = {
  id: 1,
  name: "Nguyễn Văn A",
  superadmin: false,
  permissions: {
    "user:browse": 1,
    "user:create": 1,
    "dish:browse": 1,
    // không có "user:delete" means không có quyền delete user
  }
};
```

---

## **2. 🔑 CÁC API ENDPOINTS QUAN TRỌNG**

### **A. Lấy thông tin user hiện tại:**
```javascript
GET /@me
Response: { id, name, email, superadmin, roles, permissions }
```

### **B. Lấy danh sách tất cả permissions:**
```javascript
GET /permissions
Response: { 
  tree: { /* cấu trúc phân cấp */ },
  flat: ["user:browse", "user:create", ...] 
}
```

### **C. Quản lý roles của user:**
```javascript
GET /users/{userId}/roles           // Xem roles của user
POST /users/{userId}/roles          // Gán roles cho user
POST /users/{userId}/roles/attach   // Thêm 1 role
DELETE /users/{userId}/roles/detach // Xóa 1 role
```

---

## **3. 🛡️ CÁCH KIỂM TRA QUYỀN**

### **Function cơ bản (PHẢI SỬ DỤNG):**
```javascript
const checkPermission = (user, requiredPermission) => {
  // Superadmin có tất cả quyền
  if (user?.superadmin) return true;
  
  // Kiểm tra permission cụ thể
  return user?.permissions?.[requiredPermission] === 1;
};

// Kiểm tra nhiều quyền (OR)
const hasAnyPermission = (user, permissions) => {
  if (user?.superadmin) return true;
  return permissions.some(perm => user?.permissions?.[perm] === 1);
};

// Kiểm tra nhiều quyền (AND)  
const hasAllPermissions = (user, permissions) => {
  if (user?.superadmin) return true;
  return permissions.every(perm => user?.permissions?.[perm] === 1);
};
```

---

## **4. 🎨 QUY TẮC CONDITIONAL RENDERING**

### **NGUYÊN TẮC BẮT BUỘC:**
1. **LUÔN LUÔN** kiểm tra quyền trước khi hiển thị UI elements
2. **LUÔN LUÔN** handle trường hợp user không có quyền
3. **LUÔN LUÔN** hiển thị message thân thiện khi không có quyền

### **Examples:**

#### **React/JSX:**
```jsx
const UserManagement = () => {
  const { user } = useAuth();
  
  return (
    <div>
      <h1>Quản lý nhân viên</h1>
      
      {/* ✅ ĐÚNG: Kiểm tra quyền trước khi hiển thị */}
      {checkPermission(user, 'user:create') && (
        <Button onClick={handleCreate}>Tạo nhân viên mới</Button>
      )}
      
      {/* ✅ ĐÚNG: Hiển thị table hoặc message */}
      {checkPermission(user, 'user:browse') ? (
        <UserTable />
      ) : (
        <Alert message="Bạn không có quyền xem danh sách nhân viên" type="info" />
      )}
      
      {/* ❌ SAI: Không bao giờ để hardcode permissions */}
      {/* user.role === 'admin' && <Button>Admin Only</Button> */}
    </div>
  );
};
```

#### **Vue:**
```vue
<template>
  <div>
    <!-- ✅ ĐÚNG: Sử dụng computed properties -->
    <el-button v-if="canCreateUser" @click="createUser">
      Tạo nhân viên mới
    </el-button>
    
    <el-table v-if="canBrowseUsers" :data="users">
      <!-- Table content -->
    </el-table>
    
    <el-alert v-else type="info" title="Bạn không có quyền xem danh sách nhân viên" />
  </div>
</template>

<script setup>
const { user } = useAuth();

// ✅ ĐÚNG: Computed properties cho permissions
const canCreateUser = computed(() => checkPermission(user.value, 'user:create'));
const canBrowseUsers = computed(() => checkPermission(user.value, 'user:browse'));
</script>
```

---

## **5. 🗂️ DYNAMIC MENU/NAVIGATION**

### **Menu Configuration Pattern:**
```javascript
const menuItems = [
  {
    key: 'users',
    title: 'Quản lý nhân viên',
    path: '/users',
    permission: 'user:browse',  // Required permission
    icon: 'UserOutlined'
  },
  {
    key: 'dishes', 
    title: 'Quản lý món ăn',
    path: '/dishes',
    permission: 'dish:browse',
    icon: 'ShopOutlined'
  },
  {
    key: 'roles',
    title: 'Phân quyền', 
    path: '/roles',
    permission: 'role:browse',
    icon: 'SafetyOutlined'
  },
  {
    key: 'settings',
    title: 'Cấu hình',
    path: '/settings', 
    permission: 'site-setting:browse',
    icon: 'SettingOutlined'
  }
];

// ✅ ĐÚNG: Filter menu dựa trên permissions
const getVisibleMenuItems = (user) => {
  return menuItems.filter(item => {
    if (!item.permission) return true; // Public menu
    return checkPermission(user, item.permission);
  });
};
```

---

## **6. 🔄 ERROR HANDLING**

### **API Error Handler (BẮT BUỘC):**
```javascript
// Axios interceptor hoặc fetch wrapper
const handleApiError = (error) => {
  if (error.status === 403) {
    // Không có quyền
    showNotification({
      type: 'error',
      message: 'Bạn không có quyền thực hiện hành động này'
    });
    return;
  }
  
  if (error.status === 401) {
    // Chưa đăng nhập
    redirectToLogin();
    return;
  }
  
  // Other errors...
};
```

---

## **7. 📋 ROLE MANAGEMENT INTERFACE**

### **Khi tạo UI quản lý roles:**
```javascript
const RoleManagement = () => {
  const [roles, setRoles] = useState([]);
  const [allPermissions, setAllPermissions] = useState({});
  
  // ✅ ĐÚNG: Lấy cả roles và permissions structure
  useEffect(() => {
    // Lấy danh sách roles
    fetch('/role').then(res => res.json()).then(setRoles);
    
    // Lấy permission structure để hiển thị 
    fetch('/permissions').then(res => res.json()).then(setAllPermissions);
  }, []);
  
  return (
    <div>
      {/* Permission Matrix */}
      {Object.entries(allPermissions.tree?.default?.children || {}).map(([resource, data]) => (
        <div key={resource}>
          <h3>{data.description}</h3>
          {Object.entries(data.actions).map(([action, actionData]) => (
            <Checkbox key={actionData.permission}>
              {actionData.description}
            </Checkbox>
          ))}
        </div>
      ))}
    </div>
  );
};
```

---

## **8. 🎯 CÁC RESOURCES VÀ ACTIONS HIỆN CÓ**

### **Main Resources:**
- `user` - Quản lý nhân viên
- `role` - Phân quyền  
- `customer` - Quản lý khách hàng
- `dish` - Quản lý món ăn
- `dish-category` - Quản lý danh mục món ăn
- `table` - Quản lý bàn
- `reservation` - Quản lý đặt bàn
- `bill` - Quản lý hóa đơn
- `promotion` - Quản lý khuyến mãi
- `ingredient` - Quản lý nguyên liệu
- `enter-ingredient` - Quản lý nhập nguyên liệu
- `export-ingredient` - Quản lý xuất nguyên liệu
- `site-setting` - Quản lý cấu hình
- `media` - Quản lý media

### **Common Actions:**
- `browse` - Xem danh sách
- `read` - Xem chi tiết
- `create` - Tạo mới
- `update` - Cập nhật  
- `delete` - Xóa
- `export` - Xuất dữ liệu
- `import` - Nhập dữ liệu

---

## **9. 🚨 LƯU Ý QUAN TRỌNG PHẢI NHỚ**

### **DO's ✅:**
1. **LUÔN** kiểm tra `user.superadmin` trước
2. **LUÔN** kiểm tra `user?.permissions?.[permission] === 1`
3. **LUÔN** có fallback UI cho trường hợp không có quyền
4. **LUÔN** sử dụng loading state khi chưa có user data
5. **LUÔN** handle 401/403 errors từ API

### **DON'Ts ❌:**
1. **KHÔNG BAO GIỜ** hardcode role names (`user.role === 'admin'`)
2. **KHÔNG BAO GIỜ** assume user luôn có quyền
3. **KHÔNG BAO GIỜ** hiển thị UI rồi mới check permission
4. **KHÔNG BAO GIỜ** quên handle loading/error states
5. **KHÔNG BAO GIỜ** bypass permission checks ở frontend

### **Performance Tips 🚀:**
1. Lưu user permissions trong context/store
2. Sử dụng computed properties cho permission checks
3. Memoize permission check functions
4. Cache menu items filtered by permissions

---

## **10. 📝 EXAMPLE IMPLEMENTATION**

### **Complete Auth Hook Example:**
```javascript
const useAuth = () => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  
  useEffect(() => {
    fetchUserInfo();
  }, []);
  
  const fetchUserInfo = async () => {
    try {
      const response = await fetch('/@me');
      const userData = await response.json();
      setUser(userData);
    } catch (error) {
      console.error('Failed to fetch user info:', error);
    } finally {
      setLoading(false);
    }
  };
  
  const checkPermission = useCallback((permission) => {
    if (!user) return false;
    if (user.superadmin) return true;
    return user.permissions?.[permission] === 1;
  }, [user]);
  
  const hasAnyPermission = useCallback((permissions) => {
    if (!user) return false;
    if (user.superadmin) return true;
    return permissions.some(perm => user.permissions?.[perm] === 1);
  }, [user]);
  
  return {
    user,
    loading,
    checkPermission,
    hasAnyPermission,
    isLoggedIn: !!user,
    isSuperAdmin: user?.superadmin || false
  };
};
```

---

## **🎯 NHIỆM VỤ CỦA BẠN KHI TẠO FRONTEND:**

1. **Tạo components có conditional rendering dựa trên permissions**
2. **Tạo dynamic menu system filtering theo quyền user**  
3. **Handle API errors cho 401/403 responses**
4. **Tạo role management interface cho admin**
5. **Implement permission check hooks/composables**
6. **Tạo loading states cho auth data**
7. **Ensure UX tốt khi user không có quyền**

**QUAN TRỌNG:** Luôn nhớ rằng frontend permission chỉ là UX, security thực sự ở backend. Nhưng frontend phải implement đúng để có UX tốt và consistent. 

APP_NAME=r0
APP_ENV=local
APP_KEY=base64:NsnX7OFspRwsaGSw3Pw/Mvpun1t2SaqUIxFBEUNMXCw=
APP_PREVIOUS_KEYS=
APP_DEBUG=true
APP_URL=https://r0.test
APP_TIMEZONE=UTC

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

#DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=r0
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.r0.test
SESSION_COOKIE=r0_session
SESSION_SAME_SITE=strict

BROADCAST_CONNECTION=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=redis
CACHE_PREFIX=cached:

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=1
REDIS_CACHE_DB=2
REDIS_PREFIX=r0_

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# VNPay Configuration (Sandbox)
VNPAY_TMN_CODE=your_tmn_code_from_vnpay
VNPAY_HASH_SECRET=your_hash_secret_from_vnpay
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=https://admin.r0.test/vnpay-return
VNPAY_IPN_URL=https://r0.test/vnpay/ipn
VNPAY_API_URL=https://sandbox.vnpayment.vn/merchant_webapi/api/transaction