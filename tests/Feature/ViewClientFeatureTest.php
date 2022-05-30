<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\ClientTrait;

class ViewClientFeatureTest extends TestCase
{
    use ClientTrait;

    private string $uri = 'api/client/{id}/view';

    /**
     * Test fail when client not found.
     *
     * @return void
     */
    public function testFail() : void
    {
        $response = $this->json('GET',
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

        $response = $this->json('GET',
            str_replace('{id}',
                $client->id,
                $this->uri));

        $response->assertStatus(200);
        $this->assertArrayHasKey('firstName',
            $response->json());
        $this->assertArrayHasKey('lastName',
            $response->json());
        $this->assertArrayHasKey('email',
            $response->json());
        $this->assertArrayHasKey('phoneNumber',
            $response->json());
        $this->assertContains($client->first_name,
            $response->json());
        $this->assertContains($client->last_name,
            $response->json());
        $this->assertContains($client->email,
            $response->json());
        $this->assertContains($client->phone_number,
            $response->json());

        //Delete test client.
        $client->delete();
    }
}