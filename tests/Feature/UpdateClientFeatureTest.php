<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use Tests\Helpers\ClientTrait;

class UpdateClientFeatureTest extends TestCase
{
    use ClientTrait;

    private string $uri = 'api/client/{id}/update';

    /**
     * Test fail when client not found.
     *
     * @return void
     */
    public function testFail() : void
    {
        $response = $this->json('PUT',
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

        $factory = Factory::create();
        $data    = [
            'firstName' => $factory->firstName,
        ];

        $response = $this->json('PUT',
            str_replace('{id}',
                $client->id,
                $this->uri),
            $data);

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Client data has been updated.',
            $response->json());
        $client->refresh();
        $this->assertEquals($client->first_name,
            $data['firstName']);

        //Delete test client.
        $client->delete();
    }
}