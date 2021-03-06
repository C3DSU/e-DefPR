<?php

namespace Tests\Feature\CounterPart;

use App\Models\CounterPart;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleTableSeeder']);
    }

    /**
     * @test Update a specific counterPart
     */
    public function testUpdate()
    {
        $admin = factory(User::class)->create();

        $admin->assignRole('master');

        $assisted = factory(CounterPart::class)->create([
            'name' => 'Test 1'
        ]);

        $response1 = $this->actingAs($admin)->get('/counter-part/' . $assisted->id);

        $response1->assertSuccessful();

        $this->assertEquals('Test 1', $response1->json()['data']['name']);

        $response2 = $this->actingAs($admin)->put('/counter-part/' . $assisted->id, [
            'name' => 'Test 2'
        ]);

        $response2->assertSuccessful();

        $response3 = $this->actingAs($admin)->get('/counter-part/' . $assisted->id);

        $response3->assertSuccessful();

        $this->assertEquals('Test 2', $response3->json()['data']['name']);
    }
}
