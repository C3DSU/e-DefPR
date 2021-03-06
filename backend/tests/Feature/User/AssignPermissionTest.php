<?php

namespace Tests\Feature\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleTableSeeder']);
    }

    /**
     * @test Assign permission to a User
     */
    public function testAssignPermission()
    {
        $admin = factory(User::class)->create();

        $admin->assignRole('master');

        $user = factory(User::class)->create();

        $permission = 'register-activities';

        $response = $this->actingAs($admin)->put("/user/$user->id/assign-permission/$permission");

        $response->assertSuccessful();

        $permission = 'dont-exist';

        $response = $this->actingAs($admin)->put("/user/$user->id/assign-permission/$permission");

        $this->assertNotEquals(200, $response->getStatusCode());

        $response = $this->actingAs($admin)->put("/user/9999/assign-permission/$permission");

        $response->assertNotFound();
    }

    /**
     * @test Assign multiple permissions to a User
     */
    public function testAssignMultiplePermissions()
    {
        $admin = factory(User::class)->create();

        $admin->assignRole('master');

        $user = factory(User::class)->create();

        $permissions = [
            'register-activities',
            'register-general-activities',
            'open-process'
        ];

        $response = $this->actingAs($admin)->put("/user/$user->id/assign-permissions", [
            'permissions' => $permissions
        ]);

        $response->assertSuccessful();

        $permissions = [
            'register-activities',
            'register-general-activities',
            'open-process',
            'dont-exist'
        ];

        $response = $this->actingAs($admin)->put("/user/$user->id/assign-permissions", [
            'permissions' => $permissions
        ]);

        $this->assertNotEquals(200, $response->getStatusCode());

        $response = $this->actingAs($admin)->put("/user/999/assign-permissions", [
            'permissions' => $permissions
        ]);

        $response->assertNotFound();
    }
}
