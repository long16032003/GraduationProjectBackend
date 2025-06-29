<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Quản trị viên',
                'level' => 10,
                'status' => true,
                'permissions' => $this->getAdminPermissions(),
            ],
            [
                'name' => 'Quản lý',
                'level' => 8,
                'status' => true,
                'permissions' => $this->getManagerPermissions(),
            ],
            [
                'name' => 'Thu ngân',
                'level' => 5,
                'status' => true,
                'permissions' => $this->getCashierPermissions(),
            ],
            [
                'name' => 'Phục vụ',
                'level' => 3,
                'status' => true,
                'permissions' => $this->getServiceStaffPermissions(),
            ],
            [
                'name' => 'Đầu bếp',
                'level' => 6,
                'status' => true,
                'permissions' => $this->getChefPermissions(),
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }

    /**
     * Quyền cho Admin - Toàn quyền
     */
    private function getAdminPermissions(): array
    {
        return [
            // User Management
            'user:browse' => true,
            'user:read' => true,
            'user:create' => true,
            'user:update' => true,
            'user:delete' => true,

            // Role Management
            'role:browse' => true,
            'role:read' => true,
            'role:create' => true,
            'role:update' => true,
            'role:delete' => true,

            // Customer Management
            'customer:browse' => true,
            'customer:read' => true,
            'customer:create' => true,
            'customer:update' => true,
            'customer:delete' => true,

            // Site Settings
            'site-setting:browse' => true,
            'site-setting:read' => true,
            'site-setting:create' => true,
            'site-setting:update' => true,
            'site-setting:delete' => true,

            // Statistics
            'statistics:browse' => true,
            'statistics:read' => true,

            // Posts
            'post:browse' => true,
            'post:read' => true,
            'post:create' => true,
            'post:update' => true,
            'post:delete' => true,

            // Dishes & Categories
            'dish:browse' => true,
            'dish:read' => true,
            'dish:create' => true,
            'dish:update' => true,
            'dish:delete' => true,
            'dish-category:browse' => true,
            'dish-category:read' => true,
            'dish-category:create' => true,
            'dish-category:update' => true,
            'dish-category:delete' => true,

            // Tables & Reservations
            'table:browse' => true,
            'table:read' => true,
            'table:create' => true,
            'table:update' => true,
            'table:delete' => true,
            'reservation:browse' => true,
            'reservation:read' => true,
            'reservation:create' => true,
            'reservation:update' => true,
            'reservation:delete' => true,

            // Bills & Promotions
            'bill:browse' => true,
            'bill:read' => true,
            'bill:create' => true,
            'bill:update' => true,
            'bill:delete' => true,
            'promotion:browse' => true,
            'promotion:read' => true,
            'promotion:create' => true,
            'promotion:update' => true,
            'promotion:delete' => true,

            // Ingredients Management
            'ingredient:browse' => true,
            'ingredient:read' => true,
            'ingredient:create' => true,
            'ingredient:update' => true,
            'ingredient:delete' => true,
            'enter-ingredient:browse' => true,
            'enter-ingredient:read' => true,
            'enter-ingredient:create' => true,
            'export-ingredient:browse' => true,
            'export-ingredient:read' => true,
            'export-ingredient:create' => true,
        ];
    }

    /**
     * Quyền cho Manager - Quản lý tổng thể
     */
    private function getManagerPermissions(): array
    {
        return [
            // User Management
            'user:browse' => true,
            'user:read' => true,
            'user:create' => true,
            'user:update' => true,

            // Customer Management
            'customer:browse' => true,
            'customer:read' => true,
            'customer:create' => true,
            'customer:update' => true,

            // Statistics (Manager can view)
            'statistics:browse' => true,
            'statistics:read' => true,

            // Posts Management
            'post:browse' => true,
            'post:read' => true,
            'post:create' => true,
            'post:update' => true,

            // Dishes & Categories
            'dish:browse' => true,
            'dish:read' => true,
            'dish:create' => true,
            'dish:update' => true,
            'dish-category:browse' => true,
            'dish-category:read' => true,
            'dish-category:create' => true,
            'dish-category:update' => true,

            // Tables & Reservations
            'table:browse' => true,
            'table:read' => true,
            'table:create' => true,
            'table:update' => true,
            'reservation:browse' => true,
            'reservation:read' => true,
            'reservation:create' => true,
            'reservation:update' => true,

            // Bills & Promotions
            'bill:browse' => true,
            'bill:read' => true,
            'bill:create' => true,
            'bill:update' => true,
            'promotion:browse' => true,
            'promotion:read' => true,
            'promotion:create' => true,
            'promotion:update' => true,

            // Ingredients Management
            'ingredient:browse' => true,
            'ingredient:read' => true,
            'ingredient:create' => true,
            'ingredient:update' => true,
            'enter-ingredient:browse' => true,
            'enter-ingredient:read' => true,
            'enter-ingredient:create' => true,
            'export-ingredient:browse' => true,
            'export-ingredient:read' => true,
            'export-ingredient:create' => true,
        ];
    }

    /**
     * Quyền cho Cashier - Thu ngân
     */
    private function getCashierPermissions(): array
    {
        return [
            'customer:browse' => true,
            'customer:read' => true,
            'customer:create' => true,
            'customer:update' => true,

            'dish:browse' => true,
            'dish:read' => true,

            'bill:browse' => true,
            'bill:read' => true,
            'bill:create' => true,
            'bill:update' => true,

            'promotion:browse' => true,
            'promotion:read' => true,

            'table:browse' => true,
            'table:read' => true,

            'reservation:browse' => true,
            'reservation:read' => true,
            'reservation:create' => true,
        ];
    }

    /**
     * Quyền cho Service Staff - Phục vụ
     */
    private function getServiceStaffPermissions(): array
    {
        return [
            'customer:browse' => true,
            'customer:read' => true,
            'customer:create' => true,

            'order:browse' => true,
            'order:read' => true,
            'order:create' => true,
            'order:update' => true,

            'dish:browse' => true,
            'dish:read' => true,

            'table:browse' => true,
            'table:read' => true,

            'reservation:browse' => true,
            'reservation:read' => true,

            'bill:browse' => true,
            'bill:read' => true,
            'bill:create' => true,
        ];
    }

    /**
     * Quyền cho Chef - Đầu bếp
     */
    private function getChefPermissions(): array
    {
        return [
            'dish:browse' => true,
            'dish:read' => true,
            'dish:create' => true,
            'dish:update' => true,

            'dish-category:browse' => true,
            'dish-category:read' => true,
            'dish-category:create' => true,
            'dish-category:update' => true,

            'ingredient:browse' => true,
            'ingredient:read' => true,
            'ingredient:create' => true,
            'ingredient:update' => true,

            'enter-ingredient:browse' => true,
            'enter-ingredient:read' => true,
            'enter-ingredient:create' => true,

            'export-ingredient:browse' => true,
            'export-ingredient:read' => true,
            'export-ingredient:create' => true,

            'bill:browse' => true,
            'bill:read' => true,

            'order:browse' => true,
            'order:read' => true,
            'order:update' => true,

            'table:browse' => true,
            'table:read' => true,
        ];
    }
}
