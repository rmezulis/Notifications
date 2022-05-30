<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use Tests\Helpers\ClientTrait;

class ViewNotificationFeatureTest extends TestCase
{
    use ClientTrait;

    private string $uri = 'api/notification/{id}';

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
     * Test fail when notification not found.
     *
     * @return void
     */
    public function testFail() : void
    {
        $user = User::factory()
            ->create();

        $response = $this->actingAs($user)
            ->json('GET',
                str_replace('{id}',
                    0,
                    $this->uri));

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',
            $response->json());
        $this->assertContains('Notification with provided ID was not found',
            $response->json());

        //Delete test data.
        $user->delete();
    }

    /**
     * Test success when notification found.
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

        $notification = Notification::create($data);

        $response = $this->actingAs($user)
            ->json('GET',
                str_replace('{id}',
                    $client->id,
                    $this->uri));

        $response->assertStatus(200);
        $this->assertArrayHasKey('clientId',
            $response->json());
        $this->assertArrayHasKey('channel',
            $response->json());
        $this->assertArrayHasKey('content',
            $response->json());
        $this->assertContains($notification->client_id,
            $response->json());
        $this->assertContains($notification->channel,
            $response->json());
        $this->assertContains($notification->content,
            $response->json());

        //Delete test data.
        $user->delete();
        $notification->delete();
        $client->delete();
    }
}