<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use App\Models\Client;
use Tests\Helpers\ClientTrait;

class CreateClientFeatureTest extends TestCase
{
    private string $uri = 'api/client/create';

    /**
     * Test fail when client not found.
     *
     * @return void
     */
    public function testFail() : void
    {
        $response = $this->json('POST',
            $this->uri);

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Failed to create Client',
            $response->json());
        $this->assertArrayHasKey('errors',
            $response->json());

        $errors = $response->json('errors');
        $this->assertArrayHasKey('firstName',
            $errors);
        $this->assertArrayHasKey('lastName',
            $errors);
        $this->assertArrayHasKey('email',
            $errors);
        $this->assertArrayHasKey('phoneNumber',
            $errors);
    }

    /**
     * Test success when client found.
     *
     * @return void
     */
    public function testSuccess() : void
    {
        $factory = Factory::create();
        $data    = [
            'firstName'   => $factory->firstName,
            'lastName'    => $factory->lastName,
            'email'       => $factory->email,
            'phoneNumber' => '+37122222222',
        ];

        $response = $this->json('POST',
            $this->uri,
            $data);

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Client has been successfully created!',
            $response->json());

        //Delete the latest client which was created in the test.
        Client::latest()
            ->first()
            ->delete();
    }
}