<?php

namespace Tests\Helpers;

use Faker\Factory;
use App\Models\Client;

trait ClientTrait
{
    public function createTestClient()
    {
        $factory = Factory::create();

        // Create test client
        return Client::create([
            'first_name' => $factory->firstName,
            'last_name' => $factory->lastName,
            'email' => $factory->email,
            'phone_number' => $factory->phoneNumber
        ]);
    }
}