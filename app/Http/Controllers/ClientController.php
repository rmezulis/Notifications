<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * @param int $id
     * @return JsonResponse
     */
    public function view(int $id) : JsonResponse
    {
        if ( !$client = Client::find($id)) {
            return response()->json([
                'message' => 'Client with provided ID was not found',
            ]);
        }

        return response()->json([
            'firstName'   => $client->first_name,
            'lastName'    => $client->last_name,
            'email'       => $client->email,
            'phoneNumber' => $client->phone_number,
        ]);
    }

    /**
     * @return mixed
     */
    public function list() : mixed
    {
        return Client::paginate();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),
            [
                'firstName'   => [
                    'required',
                    'min:2',
                    'max:32',
                ],
                'lastName'    => [
                    'required',
                    'min:2',
                    'max:32',
                ],
                'email'       => [
                    'required',
                    'email',
                    'unique:clients,email',
                ],
                'phoneNumber' => [
                    'required',
                    'regex:/^\+[1-9]\d{1,14}$/',
                ],
            ]);
        if ($validator->fails()) {
            $errors = $validator->errors()
                ->messages();

            return response()->json([
                'message' => 'Failed to create Client',
                'errors'  => $errors,
            ]);
        }
        Client::create($this->getClientDataFromRequest($request));

        return response()->json([
            'message' => 'Client has been successfully created!',
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id) : JsonResponse
    {
        if ( !$client = Client::find($id)) {
            return response()->json([
                'message' => 'Client with provided ID was not found',
            ]);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client has been successfully deleted.',
        ]);
    }

    /**
     * @param int     $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(
        int $id,
        Request $request
    ) : JsonResponse {
        if ( !$client = Client::find($id)) {
            return response()->json([
                'message' => 'Client with provided ID was not found',
            ]);
        }

        $validator = Validator::make($request->all(),
            [
                'firstName'   => [
                    'min:2',
                    'max:32',
                ],
                'lastName'    => [
                    'min:2',
                    'max:32',
                ],
                'email'       => [
                    'email',
                ],
                'phoneNumber' => [
                    'regex:/^\+[1-9]\d{1,14}$/',
                ],
            ]);

        if ($validator->fails()) {
            $errors = $validator->errors()
                ->messages();

            return response()->json([
                'message' => 'Failed to update Client',
                'errors'  => $errors,
            ]);
        }

        $client->update($this->getClientDataFromRequest($request));

        return response()->json([
            'message' => 'Client data has been updated.',
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getClientDataFromRequest(Request $request) : array
    {
        $data = [];

        if ($firstName = $request->get('firstName')) {
            $data['first_name'] = $firstName;
        }

        if ($lastName = $request->get('lastName')) {
            $data['last_name'] = $lastName;
        }

        if ($email = $request->get('email')) {
            $data['email'] = $email;
        }

        if ($phoneNumber = $request->get('phoneNumber')) {
            $data['phone_number'] = $phoneNumber;
        }

        return $data;
    }
}