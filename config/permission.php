<?php return array (
  'tree' => 
  array (
    'default' => 
    array (
      'type' => 'group',
      'name' => 'default',
      'description' => 'Default',
      'actions' => 
      array (
      ),
      'children' => 
      array (
        'user' => 
        array (
          'type' => 'resource',
          'name' => 'user',
          'description' => 'Quản lý nhân viên',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'user:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'user:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'user:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'user:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'user:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'user:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'role' => 
        array (
          'type' => 'resource',
          'name' => 'role',
          'description' => 'Phân quyền',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'role:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'role:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'role:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'role:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'role:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'role:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'customer' => 
        array (
          'type' => 'resource',
          'name' => 'customer',
          'description' => 'Quản lý khách hàng',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'customer:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'customer:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'customer:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'customer:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'customer:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'customer:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'post' => 
        array (
          'type' => 'resource',
          'name' => 'post',
          'description' => 'Quản lý bài viết',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'post:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'post:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'post:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'post:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'post:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'post:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'order' => 
        array (
          'type' => 'resource',
          'name' => 'order',
          'description' => 'Quản lý đơn gọi món',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'order:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'order:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'order:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'order:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'order:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'order:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'ingredient' => 
        array (
          'type' => 'resource',
          'name' => 'ingredient',
          'description' => 'Quản lý nguyên liệu',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'ingredient:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'ingredient:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'ingredient:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'ingredient:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'ingredient:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'ingredient:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'product' => 
        array (
          'type' => 'resource',
          'name' => 'product',
          'description' => 'Quản lý sản phẩm',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'product:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'product:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'product:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'product:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'product:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'product:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'bill' => 
        array (
          'type' => 'resource',
          'name' => 'bill',
          'description' => 'Quản lý hóa đơn',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'bill:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'bill:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'bill:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'bill:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'bill:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'bill:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'site-setting' => 
        array (
          'type' => 'resource',
          'name' => 'site-setting',
          'description' => 'Quản lý cấu hình',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'site-setting:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'site-setting:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'site-setting:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'site-setting:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'site-setting:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'site-setting:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'enter-ingredient' => 
        array (
          'type' => 'resource',
          'name' => 'enter-ingredient',
          'description' => 'Quản lý nhập nguyên liệu',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'enter-ingredient:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'enter-ingredient:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'enter-ingredient:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'enter-ingredient:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'enter-ingredient:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'enter-ingredient:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'export-ingredient' => 
        array (
          'type' => 'resource',
          'name' => 'export-ingredient',
          'description' => 'Quản lý xuất nguyên liệu',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'export-ingredient:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'export-ingredient:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'export-ingredient:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'export-ingredient:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'export-ingredient:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'export-ingredient:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'dish' => 
        array (
          'type' => 'resource',
          'name' => 'dish',
          'description' => 'Quản lý món ăn',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'dish:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'dish:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'dish:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'dish:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'dish:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'dish:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'dish-category' => 
        array (
          'type' => 'resource',
          'name' => 'dish-category',
          'description' => 'Quản lý danh mục món ăn',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'dish-category:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'dish-category:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'dish-category:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'dish-category:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'dish-category:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'dish-category:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'reservation' => 
        array (
          'type' => 'resource',
          'name' => 'reservation',
          'description' => 'Quản lý đặt bàn',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'reservation:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'reservation:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'reservation:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'reservation:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'reservation:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'reservation:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'table' => 
        array (
          'type' => 'resource',
          'name' => 'table',
          'description' => 'Quản lý bàn',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'table:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'table:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'table:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'table:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'table:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'table:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'promotion' => 
        array (
          'type' => 'resource',
          'name' => 'promotion',
          'description' => 'Quản lý khuyến mãi',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'promotion:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'promotion:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'promotion:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'promotion:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'promotion:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'promotion:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'staff' => 
        array (
          'type' => 'resource',
          'name' => 'staff',
          'description' => 'Quản lý nhân viên',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'staff:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'staff:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'staff:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'staff:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'staff:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'staff:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'media' => 
        array (
          'type' => 'resource',
          'name' => 'media',
          'description' => 'Quản lý media',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'media:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'media:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tải lên',
              'permission' => 'media:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'media:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'media:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'media:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'statistics' => 
        array (
          'type' => 'resource',
          'name' => 'statistics',
          'description' => 'Thống kê',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Xem danh sách',
              'permission' => 'statistics:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'Xem chi tiết',
              'permission' => 'statistics:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Tạo',
              'permission' => 'statistics:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Cập nhật',
              'permission' => 'statistics:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Xóa',
              'permission' => 'statistics:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Sao chép',
              'permission' => 'statistics:clone',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
      ),
    ),
  ),
  'flat' => 
  array (
    0 => 'user:browse',
    1 => 'user:read',
    2 => 'user:create',
    3 => 'user:update',
    4 => 'user:delete',
    5 => 'user:clone',
    6 => 'role:browse',
    7 => 'role:read',
    8 => 'role:create',
    9 => 'role:update',
    10 => 'role:delete',
    11 => 'role:clone',
    12 => 'customer:browse',
    13 => 'customer:read',
    14 => 'customer:create',
    15 => 'customer:update',
    16 => 'customer:delete',
    17 => 'customer:clone',
    18 => 'post:browse',
    19 => 'post:read',
    20 => 'post:create',
    21 => 'post:update',
    22 => 'post:delete',
    23 => 'post:clone',
    24 => 'order:browse',
    25 => 'order:read',
    26 => 'order:create',
    27 => 'order:update',
    28 => 'order:delete',
    29 => 'order:clone',
    30 => 'ingredient:browse',
    31 => 'ingredient:read',
    32 => 'ingredient:create',
    33 => 'ingredient:update',
    34 => 'ingredient:delete',
    35 => 'ingredient:clone',
    36 => 'product:browse',
    37 => 'product:read',
    38 => 'product:create',
    39 => 'product:update',
    40 => 'product:delete',
    41 => 'product:clone',
    42 => 'bill:browse',
    43 => 'bill:read',
    44 => 'bill:create',
    45 => 'bill:update',
    46 => 'bill:delete',
    47 => 'bill:clone',
    48 => 'site-setting:browse',
    49 => 'site-setting:read',
    50 => 'site-setting:create',
    51 => 'site-setting:update',
    52 => 'site-setting:delete',
    53 => 'site-setting:clone',
    54 => 'enter-ingredient:browse',
    55 => 'enter-ingredient:read',
    56 => 'enter-ingredient:create',
    57 => 'enter-ingredient:update',
    58 => 'enter-ingredient:delete',
    59 => 'enter-ingredient:clone',
    60 => 'export-ingredient:browse',
    61 => 'export-ingredient:read',
    62 => 'export-ingredient:create',
    63 => 'export-ingredient:update',
    64 => 'export-ingredient:delete',
    65 => 'export-ingredient:clone',
    66 => 'dish:browse',
    67 => 'dish:read',
    68 => 'dish:create',
    69 => 'dish:update',
    70 => 'dish:delete',
    71 => 'dish:clone',
    72 => 'dish-category:browse',
    73 => 'dish-category:read',
    74 => 'dish-category:create',
    75 => 'dish-category:update',
    76 => 'dish-category:delete',
    77 => 'dish-category:clone',
    78 => 'reservation:browse',
    79 => 'reservation:read',
    80 => 'reservation:create',
    81 => 'reservation:update',
    82 => 'reservation:delete',
    83 => 'reservation:clone',
    84 => 'table:browse',
    85 => 'table:read',
    86 => 'table:create',
    87 => 'table:update',
    88 => 'table:delete',
    89 => 'table:clone',
    90 => 'promotion:browse',
    91 => 'promotion:read',
    92 => 'promotion:create',
    93 => 'promotion:update',
    94 => 'promotion:delete',
    95 => 'promotion:clone',
    96 => 'staff:browse',
    97 => 'staff:read',
    98 => 'staff:create',
    99 => 'staff:update',
    100 => 'staff:delete',
    101 => 'staff:clone',
    102 => 'media:browse',
    103 => 'media:read',
    104 => 'media:create',
    105 => 'media:update',
    106 => 'media:delete',
    107 => 'media:clone',
    108 => 'statistics:browse',
    109 => 'statistics:read',
    110 => 'statistics:create',
    111 => 'statistics:update',
    112 => 'statistics:delete',
    113 => 'statistics:clone',
  ),
);