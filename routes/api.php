<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/client')
    ->group(function ()
    {
        Route::post('/create',
            [
                ClientController::class,
                'create',
            ]);
        Route::get('/{id}/view',
            [
                ClientController::class,
                'view',
            ]);
        Route::put('/{id}/update',
            [
                ClientController::class,
                'update',
            ]);
        Route::delete('/{id}/delete',
            [
                ClientController::class,
                'delete',
            ]);
        Route::middleware('auth:sanctum')->get('/list',
            [
                ClientController::class,
                'list',
            ]);
    });
Route::middleware('auth:sanctum')->prefix('notification')
    ->group(function ()
    {
        Route::get('/list',
            [
                NotificationController::class,
                'list',
            ]);
        Route::post('/create',
            [
                NotificationController::class,
                'create',
            ]);
        Route::get('/{id}',
            [
                NotificationController::class,
                'view',
            ]);
    });
Route::get('not-authenticated',
    function ()
    {
        return 'You must be authenticated to perform this action.';
    })
    ->name('no-auth');

Route::post('login',
    function (Request $request)
    {
        if (Auth::attempt($request->all())) {
            Auth::login($user = User::where('email',
                $request->get('email'))->first());

            return response()->json([
                'message' => 'Logged in successfully.',
                'token' => $user->createToken('mytoken')->plainTextToken
            ]);
        } else {
            return response()->json(['message' => 'Something went wrong, please try again.']);
        }
    });