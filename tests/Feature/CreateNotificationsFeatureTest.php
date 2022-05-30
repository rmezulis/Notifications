<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use Tests\Helpers\ClientTrait;

class CreateNotificationsFeatureTest extends TestCase
{
    use ClientTrait;

    private string $uri = 'api/notification/create';

    /**
     * Test fail when not authenticated.
     *
     * @return void
     */
    public function testFailWhenNotAuthenticated() : void
    {
        $response = $this->json('POST',
            $this->uri);

        $response->assertStatus(401);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Unauthenticated.',
            $response->json());
    }

    /**
     * Test fail when notification not created.
     *
     * @return void
     */
    public function testFailWhenMissingData() : void
    {
        $user = User::factory()
            ->create();

        $response = $this->actingAs($user)
            ->json('POST',
                $this->uri);

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Failed to create Notification',
            $response->json());
        $this->assertArrayHasKey('errors',
            $response->json());
        $this->assertArrayHasKey('clientId',
            $response->json('errors'));
        $this->assertArrayHasKey('channel',
            $response->json('errors'));
        $this->assertArrayHasKey('content',
            $response->json('errors'));

        $user->delete();
    }

    /**
     * Test success when notification is created.
     *
     * @return void
     */
    public function testSuccess() : void
    {
        $user = User::factory()
            ->create();

        $client = $this->createTestClient();

        $factory = Factory::create();
        $data    = [
            'clientId' => $client->id,
            'channel'  => $factory->randomElement([
                Notification::CHANNEL_SMS,
                Notification::CHANNEL_EMAIL,
            ]),
            'content'  => $factory->text(120),
        ];

        $response = $this->actingAs($user)
            ->json('POST',
                $this->uri,
                $data);

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Notification has been created and sent successfully',
            $response->json());

        //Delete test data.
        $user->delete();
        Notification::latest()
            ->first()
            ->delete();
        $client->delete();
    }
}