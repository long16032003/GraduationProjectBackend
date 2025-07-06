<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function user_with_permission_can_access_protected_route()
    {
        // Create user with admin role
        $user = User::factory()->create();
        $adminRole = Role::where('key', 'admin')->first();
        $user->assignRole($adminRole);

        // Test accessing protected route
        $response = $this->actingAs($user)
            ->getJson('/role');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_permission_cannot_access_protected_route()
    {
        // Create user with waiter role (no role:browse permission)
        $user = User::factory()->create();
        $waiterRole = Role::where('key', 'waiter')->first();
        $user->assignRole($waiterRole);

        // Test accessing protected route
        $response = $this->actingAs($user)
            ->getJson('/role');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ]);
    }

    /** @test */
    public function superadmin_can_access_all_routes()
    {
        // Create superadmin user
        $user = User::factory()->create(['superadmin' => true]);

        // Test accessing protected route without any role
        $response = $this->actingAs($user)
            ->getJson('/role');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_permission_api_returns_correct_permissions()
    {
        // Create user with cashier role
        $user = User::factory()->create();
        $cashierRole = Role::where('key', 'cashier')->first();
        $user->assignRole($cashierRole);

        // Test user permissions API
        $response = $this->actingAs($user)
            ->getJson('/user-permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'roles',
                    'permissions',
                    'flat_permissions'
                ]
            ]);

        // Check if cashier permissions are included
        $permissions = $response->json('data.permissions');
        $this->assertArrayHasKey('bill:browse', $permissions);
        $this->assertArrayHasKey('bill:create', $permissions);
    }

    /** @test */
    public function permission_check_api_works_correctly()
    {
        // Create user with manager role
        $user = User::factory()->create();
        $managerRole = Role::where('key', 'manager')->first();
        $user->assignRole($managerRole);

        // Test permission check API - should have dish:create
        $response = $this->actingAs($user)
            ->postJson('/user-permissions/check', [
                'permission' => 'dish:create'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'has_permission' => true,
                    'permission' => 'dish:create'
                ]
            ]);

        // Test permission check API - should NOT have role:delete
        $response = $this->actingAs($user)
            ->postJson('/user-permissions/check', [
                'permission' => 'role:delete'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'has_permission' => false,
                    'permission' => 'role:delete'
                ]
            ]);
    }

    /** @test */
    public function check_any_permissions_api_works_correctly()
    {
        // Create user with chef role
        $user = User::factory()->create();
        $chefRole = Role::where('key', 'chef')->first();
        $user->assignRole($chefRole);

        // Test check-any API - should have at least one dish permission
        $response = $this->actingAs($user)
            ->postJson('/user-permissions/check-any', [
                'permissions' => ['dish:create', 'dish:update', 'role:create']
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'has_any_permission' => true
                ]
            ]);
    }

    /** @test */
    public function check_all_permissions_api_works_correctly()
    {
        // Create user with admin role
        $user = User::factory()->create();
        $adminRole = Role::where('key', 'admin')->first();
        $user->assignRole($adminRole);

        // Test check-all API - admin should have all permissions
        $response = $this->actingAs($user)
            ->postJson('/user-permissions/check-all', [
                'permissions' => ['user:create', 'user:update', 'role:browse']
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'has_all_permissions' => true
                ]
            ]);
    }
}
