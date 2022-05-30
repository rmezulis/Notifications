<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use App\Notifications\SmsNotification;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * @param int $id
     * @return JsonResponse
     */
    public function view(int $id) : JsonResponse
    {
        if ( !$notification = Notification::find($id)) {
            return response()->json([
                'message' => 'Notification with the provided ID was not found.',
            ]);
        }

        return response()->json([
            'clientId' => $notification->client_id,
            'channel'  => $notification->channel,
            'content'  => $notification->content,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(Request $request) : \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Notification::query()
            ->when($request->get('clientId'),
                function ($query, $request)
                {
                    $query->where('client_id',
                        $request->get('clientId'));
                })
            ->paginate();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),
            [
                'clientId' => [
                    'required',
                    'exists:clients,id',
                ],
                'channel'  => [
                    'required',
                    'in:SMS,EMAIL',
                ],
                'content'  => ['required'],
            ]);

        if ($validator->fails()) {
            $errors = $validator->errors()
                ->messages();

            return response()->json([
                'message' => 'Failed to create Notification',
                'errors'  => $errors,
            ]);
        }

        $notification = Notification::create([
            'client_id' => $request->get('clientId'),
            'channel'   => $request->get('channel'),
            'content'   => $request->get('content'),
            'status'    => Notification::STATUS_CREATED,
        ]);

        if ($notification->channel === Notification::CHANNEL_EMAIL) {
            $notificationContent = new EmailNotification($notification->content);
        } elseif ($notification->channel === Notification::CHANNEL_SMS) {
            $notificationContent = new SmsNotification($notification->content);
        } else {
            throw new \Exception('Failed to create notification');
        }

        $notification->client->notify($notificationContent);

        $notification->update(['status' => Notification::STATUS_SENT]);

        return response()->json([
            'message' => 'Notification has been created and sent successfully',
        ]);
    }
}