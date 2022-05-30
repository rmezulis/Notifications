<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use App\Models\Client;
use Tests\Helpers\ClientTrait;

class DeleteClientFeatureTest extends TestCase
{
    use ClientTrait;

    private string $uri = 'api/client/{id}/delete';

    /**
     * Test fail when client not found.
     *
     * @return void
     */
    public function testFail() : void
    {
        $response = $this->json('DELETE',
            str_replace('{id}',
                0,
                $this->uri));

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Client with provided ID was not found',
            $response->json());
    }

    /**
     * Test success when client found.
     *
     * @return void
     */
    public function testSuccess() : void
    {
        $client = $this->createTestClient();

        $response = $this->json('DELETE',
            str_replace('{id}',
                $client->id,
                $this->uri));

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Client has been successfully deleted.',
            $response->json());

        $this->assertDatabaseMissing(Client::class,
            $client->toArray());
    }
}