# Hướng dẫn Frontend - Hệ thống phân quyền

## 🎯 Tổng quan cho Frontend Developer

### **Những điều Frontend cần quan tâm:**

## 1. **🔐 Authentication & User Info**

### **Lấy thông tin user hiện tại:**
```javascript
GET /@me
Response: {
  "id": 1,
  "name": "Nguyễn Văn A",
  "email": "admin@example.com",
  "superadmin": true,
  "roles": [
    {
      "id": 1,
      "name": "Quản trị viên",
      "key": "admin",
      "level": 10,
      "permissions": {
        "user:browse": true,
        "user:create": true,
        "user:update": true,
        "user:delete": true,
        // ... other permissions
      }
    }
  ],
  "permissions": {
    "user:browse": 1,
    "user:create": 1,
    "user:update": 1,
    "user:delete": 1,
    // ... (tất cả permissions user có)
  }
}
```

## 2. **📜 Lấy danh sách tất cả permissions có thể:**
```javascript
GET /permissions
Response: {
  "tree": {
    "default": {
      "children": {
        "user": {
          "description": "Quản lý nhân viên",
          "actions": {
            "browse": {"permission": "user:browse", "description": "Browse"},
            "create": {"permission": "user:create", "description": "Create"},
            "update": {"permission": "user:update", "description": "Update"},
            "delete": {"permission": "user:delete", "description": "Delete"}
          }
        },
        "dish": {
          "description": "Quản lý món ăn",
          "actions": {
            "browse": {"permission": "dish:browse", "description": "Browse"},
            "create": {"permission": "dish:create", "description": "Create"}
          }
        }
      }
    }
  },
  "flat": [
    "user:browse",
    "user:create", 
    "user:update",
    "user:delete",
    "dish:browse",
    "dish:create",
    // ... all permissions
  ]
}
```

## 3. **🛡️ Cách kiểm tra quyền trong Frontend:**

### **A. Kiểm tra permissions từ user data:**
```javascript
// Utils function để kiểm tra quyền
const checkPermission = (userPermissions, requiredPermission) => {
  // Nếu user là superadmin thì có tất cả quyền
  if (user.superadmin) return true;
  
  // Kiểm tra có permission cụ thể
  return userPermissions[requiredPermission] === 1;
};

// Kiểm tra nhiều quyền (OR - có ít nhất 1 quyền)
const hasAnyPermission = (userPermissions, permissions) => {
  if (user.superadmin) return true;
  return permissions.some(permission => userPermissions[permission] === 1);
};

// Kiểm tra nhiều quyền (AND - có tất cả quyền)
const hasAllPermissions = (userPermissions, permissions) => {
  if (user.superadmin) return true;
  return permissions.every(permission => userPermissions[permission] === 1);
};

// Sử dụng
const canCreateUser = checkPermission(user.permissions, 'user:create');
const canManageUsers = hasAnyPermission(user.permissions, ['user:create', 'user:update', 'user:delete']);
```

### **B. API để kiểm tra quyền realtime:**
```javascript
POST /users/{userId}/check-permission
{
  "permission": "user:create"
}

Response: {
  "success": true,
  "data": {
    "user_id": 1,
    "permission": "user:create", 
    "has_permission": true,
    "is_superadmin": false
  }
}
```

## 4. **🎨 Conditional Rendering based on Permissions:**

### **React Example:**
```jsx
import { useState, useEffect } from 'react';

// Custom hook để lấy user permissions
const useAuth = () => {
  const [user, setUser] = useState(null);
  
  useEffect(() => {
    // Lấy thông tin user khi component mount
    fetch('/@me')
      .then(res => res.json())
      .then(userData => setUser(userData));
  }, []);
  
  const checkPermission = (permission) => {
    if (!user) return false;
    if (user.superadmin) return true;
    return user.permissions[permission] === 1;
  };
  
  return { user, checkPermission };
};

// Component với conditional rendering
const UserManagement = () => {
  const { user, checkPermission } = useAuth();
  
  if (!user) return <div>Loading...</div>;
  
  return (
    <div>
      <h1>Quản lý nhân viên</h1>
      
      {/* Chỉ hiển thị nút tạo user nếu có quyền */}
      {checkPermission('user:create') && (
        <button onClick={handleCreateUser}>
          Tạo nhân viên mới
        </button>
      )}
      
      {/* Chỉ hiển thị bảng user nếu có quyền browse */}
      {checkPermission('user:browse') ? (
        <UserTable />
      ) : (
        <div>Bạn không có quyền xem danh sách nhân viên</div>
      )}
    </div>
  );
};

// Component UserTable với conditional actions
const UserTable = () => {
  const { checkPermission } = useAuth();
  
  return (
    <table>
      <thead>
        <tr>
          <th>Tên</th>
          <th>Email</th>
          {checkPermission('user:update') && <th>Hành động</th>}
        </tr>
      </thead>
      <tbody>
        {users.map(user => (
          <tr key={user.id}>
            <td>{user.name}</td>
            <td>{user.email}</td>
            {checkPermission('user:update') && (
              <td>
                <button onClick={() => editUser(user.id)}>Sửa</button>
                {checkPermission('user:delete') && (
                  <button onClick={() => deleteUser(user.id)}>Xóa</button>
                )}
              </td>
            )}
          </tr>
        ))}
      </tbody>
    </table>
  );
};
```

### **Vue.js Example:**
```vue
<template>
  <div>
    <h1>Quản lý nhân viên</h1>
    
    <!-- Conditional rendering với v-if -->
    <button v-if="canCreate" @click="createUser">
      Tạo nhân viên mới
    </button>
    
    <table v-if="canBrowse">
      <thead>
        <tr>
          <th>Tên</th>
          <th>Email</th>
          <th v-if="canUpdate">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td v-if="canUpdate">
            <button @click="editUser(user.id)">Sửa</button>
            <button v-if="canDelete" @click="deleteUser(user.id)">Xóa</button>
          </td>
        </tr>
      </tbody>
    </table>
    
    <div v-else>
      Bạn không có quyền xem danh sách nhân viên
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const user = ref(null)
const users = ref([])

// Computed properties cho permissions
const canBrowse = computed(() => checkPermission('user:browse'))
const canCreate = computed(() => checkPermission('user:create'))
const canUpdate = computed(() => checkPermission('user:update'))
const canDelete = computed(() => checkPermission('user:delete'))

const checkPermission = (permission) => {
  if (!user.value) return false
  if (user.value.superadmin) return true
  return user.value.permissions[permission] === 1
}

onMounted(async () => {
  // Lấy thông tin user
  const response = await fetch('/@me')
  user.value = await response.json()
})
</script>
```

## 5. **🗂️ Menu/Navigation Permissions:**

```javascript
// Menu configuration với permissions
const menuItems = [
  {
    title: 'Quản lý nhân viên',
    path: '/users',
    permission: 'user:browse',
    icon: 'users'
  },
  {
    title: 'Quản lý món ăn', 
    path: '/dishes',
    permission: 'dish:browse',
    icon: 'utensils'
  },
  {
    title: 'Phân quyền',
    path: '/roles',
    permission: 'role:browse', 
    icon: 'shield'
  },
  {
    title: 'Cấu hình',
    path: '/settings',
    permission: 'site-setting:browse',
    icon: 'settings'
  }
];

// Filter menu dựa trên permissions
const getVisibleMenuItems = (userPermissions) => {
  return menuItems.filter(item => {
    if (!item.permission) return true; // Menu public
    return checkPermission(userPermissions, item.permission);
  });
};
```

## 6. **🔄 API Error Handling:**

```javascript
// Interceptor để handle permission errors
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 403) {
      // User không có quyền
      showNotification('Bạn không có quyền thực hiện hành động này', 'error');
      
      // Có thể redirect về trang chính hoặc disable UI
      return Promise.reject(error);
    }
    
    if (error.response?.status === 401) {
      // User chưa đăng nhập
      redirectToLogin();
      return Promise.reject(error);
    }
    
    return Promise.reject(error);
  }
);
```

## 7. **📋 Role Management Interface:**

```javascript
// Component để quản lý roles của user
const UserRoleManager = ({ userId }) => {
  const [userRoles, setUserRoles] = useState([]);
  const [availableRoles, setAvailableRoles] = useState([]);
  
  // Lấy roles hiện tại của user
  const fetchUserRoles = async () => {
    const response = await fetch(`/users/${userId}/roles`);
    const data = await response.json();
    setUserRoles(data.data.roles);
  };
  
  // Gán roles cho user
  const assignRoles = async (roleIds) => {
    await fetch(`/users/${userId}/roles`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ role_ids: roleIds })
    });
    
    await fetchUserRoles(); // Refresh
  };
  
  // Thêm 1 role
  const addRole = async (roleId) => {
    await fetch(`/users/${userId}/roles/attach`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ role_id: roleId })
    });
    
    await fetchUserRoles();
  };
  
  // Xóa 1 role  
  const removeRole = async (roleId) => {
    await fetch(`/users/${userId}/roles/detach`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ role_id: roleId })
    });
    
    await fetchUserRoles();
  };
  
  return (
    <div>
      {/* UI để chọn và gán roles */}
    </div>
  );
};
```

## 8. **🎛️ Permission Matrix Component:**

```javascript
// Component hiển thị matrix permissions
const PermissionMatrix = ({ roleId }) => {
  const [permissions, setPermissions] = useState({});
  const [allPermissions, setAllPermissions] = useState({});
  
  const togglePermission = (permission) => {
    setPermissions(prev => ({
      ...prev,
      [permission]: !prev[permission]
    }));
  };
  
  return (
    <div className="permission-matrix">
      {Object.entries(allPermissions.tree.default.children).map(([resource, data]) => (
        <div key={resource} className="permission-group">
          <h3>{data.description}</h3>
          
          {Object.entries(data.actions).map(([action, actionData]) => (
            <label key={actionData.permission} className="permission-item">
              <input
                type="checkbox"
                checked={permissions[actionData.permission] || false}
                onChange={() => togglePermission(actionData.permission)}
              />
              {actionData.description}
            </label>
          ))}
        </div>
      ))}
    </div>
  );
};
```

## **🎯 TÓM TẮT - Những điều quan trọng Frontend cần làm:**

1. **🔐 Lấy thông tin user & permissions** khi login/app start
2. **✅ Kiểm tra permissions** trước khi hiển thị UI elements  
3. **🎨 Conditional rendering** cho buttons, menus, pages
4. **🛡️ Handle API errors** cho 401/403 responses
5. **🔄 Real-time permission checks** nếu cần thiết
6. **📋 Role management interface** cho admin
7. **🗂️ Dynamic menu/navigation** dựa trên permissions

### **📊 Permission Format cần nhớ:**
- Format: `"resource:action"` (VD: `"user:create"`)
- User permissions: `{"permission": 1}` (1 = có quyền)
- Superadmin: `user.superadmin = true` (có tất cả quyền) 
