<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*
We would get a request for authentication using a user's phone number that we need to authenticate
 */
Route::post('/login', [LoginController::class, 'submit']);

//handle the verification of the one-time use code that was given to the user who tried to login in with 'submit()' above
Route::post('/login/verify', [LoginController::class, 'verify']);

//we will be having several routes that use tokens in this app, so it makes sense to create a route group
//The '/user' route grabs a user after authenticating the request using a token that was created for that user 
//when the user verified their phone number using the secret sms code sent to them.
Route::group(['middleware' => 'auth:sanctum'], function() {
        //create or get & update a user
        Route::get('/driver', [DriverController::class, 'show']);
        Route::post('/driver', [DriverController::class, 'update']);

        //create & get a trip
        Route::post('/trip', [TripController::class, 'store']);
        Route::get('/trip/{trip}', [TripController::class, 'show']);

        //We need a route for when a driver accepts, starts, ends a trip, & we need to update the driver's location
        Route::post('/trip/{trip}/accept', [TripController::class, 'accept'])->middleware('cors');
        Route::post('/trip/{trip}/start', [TripController::class, 'start']);
        Route::post('/trip/{trip}/end', [TripController::class, 'end']);
        Route::post('/trip/{trip}/location', [TripController::class, 'location']);

        Route::get('/user', function(Request $request)
        {
            return $request->user();
        });
    }
);

/*Route::middleware(['cors, auth:sanctum'])->group(function() {
    //create or get & update a user
    Route::get('/driver', [DriverController::class, 'show']);
    Route::post('/driver', [DriverController::class, 'update']);

    //create & get a trip
    Route::post('/trip', [TripController::class, 'store']);
    Route::get('/trip/{trip}', [TripController::class, 'show']);

    //We need a route for when a driver accepts, starts, ends a trip, & we need to update the driver's location
    Route::post('/trip/{trip}/accept', [TripController::class, 'accept']);
    Route::post('/trip/{trip}/start', [TripController::class, 'start']);
    Route::post('/trip/{trip}/end', [TripController::class, 'end']);
    Route::post('/trip/{trip}/location', [TripController::class, 'location']);

    Route::get('/user', function(Request $request)
    {
        return $request->user();
    });
});*/


