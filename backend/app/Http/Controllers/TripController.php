<?php

namespace App\Http\Controllers;

use App\Events\TripCreated;
use App\Events\TripAccepted;
use App\Events\TripStarted;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use Illuminate\Http\Request;
use App\Models\Trip;

class TripController extends Controller
{
    /**
     * Make a post request to this route like so:
     * http POST 127.0.0.1:80/api/trip 'Authorization: Bearer 1|lqWv7CfWda4QHyvlmWueXQBXIbmUD2j4EVWsLbxv' 
     *  destination_name=Starbucks 
     *  destination:='{"lat": 12.323536,"lng": 23.359230}' 
     *  origin:='{"lat": 35.293583,"lng": 39.203905}'
     */
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        TripCreated::dispatch($trip, $request->user()); 

        return $trip;
    }

    /**
     * Retrieve a previously stored trip record with an API GET request, where '/1' after the 'api/trip' 
     * refers to the id of the desired record like so:
     * 
     * http GET 127.0.0.1:80/api/trip/1 'Authorization: Bearer 1|lqWv7CfWda4QHyvlmWueXQBXIbmUD2j4EVWsLbxv'
     * 
     * Notice how the result object will now have the assoc user object with it 
     */
    public function show(Request $request, Trip $trip)
    {
        //is the trip is associated with the authenticated user?
       //this can be done with the user id & a driver id in the Trip object 
       if ($trip->user->id == $request->user()->id)
       {
         return $trip;
       }

       //the trip may not have been accepted by a user yet, which also means there wouldn't be any user 
       //assoc with the trip yet, so b4 you return any drivers for this trip, check if it has any first
       if ($trip->driver && $request->user()->driver)
       {
            if ($trip->driver->id == $request->user()->driver->id)
            {
                return $trip;
            }
        }

       return response()->json(['message' => 'Cannot find this trip'], 404);
    }


    public function accept(Request $request, Trip $trip)
    {
        die(var_dump($request));//////////
        //a driver accepts a trip
        //A driver is not naturally asscoc with  a trip, so we need to make that association as soon as the driver accpts the trip
        //the user's token will then be assoc with this trip model as being the driver in hthat trip (journey)
        $request->validate([
            'driver_locati=on' => 'required'
        ]);
        
        $trip->update([
            'driver_id' => $request->user()->id,
            //we need to record the driver's loc so we know where they're starting from
            'driver_location' => $request->driver_location,
        ]);

        //we need to load thew driver's info onto this trip model so the pasenger can view that info
        //to know who their driver is. But we will like to know the user details of the driver like name, which are 
        //not on the driver object. The best way is to load the driver, & load the user assoc with that driver object 
        $trip->load('driver.user');

        //Fire off an event to the frontend so that it knows & can therefore 
        //inform users that their trip has been picked up by a driver.
        //we dispatch info about both the trip & the user assoc with it, 
        //as there will be many passengers & drivers involved in this app 
        //& so we need to be precise
        TripAccepted::dispatch($trip, $request->user());

        return $trip;
    }


    public function start(Request $request, Trip $trip)
    {
        //driver has startred taking a passenger to their destination
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip;
    }


    public function end(Request $request, Trip $trip)
    {
        //a driver has ended a trip
        $trip->update([
            'is_complete' => true
        ]);

        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user());

        return $trip;
    }


    public function location(Request $request, Trip $trip)
    {
        //update a driver's current location
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripLocationUpdated::dispatch($trip, $request->user());

        return $trip;
    }
}
