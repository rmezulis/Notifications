<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tests\Helpers\ClientTrait;

class ListClientFeatureTest extends TestCase
{
    use ClientTrait;

    private string $uri = 'api/client/list';

    /**
     * Test fail when not authenticated.
     *
     * @return void
     */
    public function testFailWhenNotAuthenticated() : void
    {
        $response = $this->json('GET',
            $this->uri);

        $response->assertStatus(401);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Unauthenticated.',
            $response->json());
    }

    /**
     * Test success when clients found.
     *
     * @return void
     */
    public function testSuccess() : void
    {
        $user = User::factory()
            ->create();

        $response = $this->actingAs($user)
            ->json('GET',
                $this->uri);

        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));

        $user->delete();
    }
}