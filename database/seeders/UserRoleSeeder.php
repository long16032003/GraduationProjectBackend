<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user nếu chưa có
        if (!User::where('email', 'admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '0123456789',
                'password' => bcrypt('password'),
                'superadmin' => true,
            ]);

            // Gán role admin cho admin user
            $adminRole = Role::where('key', 'admin')->first();
            if ($adminRole) {
                $admin->assignRole($adminRole);
            }
        }

        // Tạo manager user
        if (!User::where('email', 'manager@example.com')->exists()) {
            $manager = User::create([
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'phone' => '0987654321',
                'password' => bcrypt('password'),
                'superadmin' => false,
            ]);

            // Gán role manager
            $manager->assignRole('manager');
        }

        // Tạo cashier user
        if (!User::where('email', 'cashier@example.com')->exists()) {
            $cashier = User::create([
                'name' => 'Cashier User',
                'email' => 'cashier@example.com',
                'phone' => '0111222333',
                'password' => bcrypt('password'),
                'superadmin' => false,
            ]);

            // Gán role cashier
            $cashier->assignRole('cashier');
        }

        // Tạo chef user
        if (!User::where('email', 'chef@example.com')->exists()) {
            $chef = User::create([
                'name' => 'Chef User',
                'email' => 'chef@example.com',
                'phone' => '0444555666',
                'password' => bcrypt('password'),
                'superadmin' => false,
            ]);

            // Gán role chef
            $chef->assignRole('chef');
        }

        // Tạo waiter user
        if (!User::where('email', 'waiter@example.com')->exists()) {
            $waiter = User::create([
                'name' => 'Waiter User',
                'email' => 'waiter@example.com',
                'phone' => '0777888999',
                'password' => bcrypt('password'),
                'superadmin' => false,
            ]);

            // Gán role waiter
            $waiter->assignRole('waiter');
        }

        // Demo: User có nhiều roles
        if (!User::where('email', 'multi@example.com')->exists()) {
            $multiUser = User::create([
                'name' => 'Multi Role User',
                'email' => 'multi@example.com',
                'phone' => '0555666777',
                'password' => bcrypt('password'),
                'superadmin' => false,
            ]);

            // Gán nhiều roles
            $multiUser->syncRoles(['cashier', 'waiter']);
        }

        $this->command->info('✅ User-Role relationships created successfully!');
        $this->command->info('👤 Admin: admin@example.com / password');
        $this->command->info('👤 Manager: manager@example.com / password');
        $this->command->info('👤 Cashier: cashier@example.com / password');
        $this->command->info('👤 Chef: chef@example.com / password');
        $this->command->info('👤 Waiter: waiter@example.com / password');
        $this->command->info('👤 Multi: multi@example.com / password');
    }
}
