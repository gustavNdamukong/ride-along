<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\LoginNeedsVerification;


class LoginController extends Controller
{
    
    public function submit(Request $request)
    {
        //validate the phone number
        //create a user model associated with it
        //send user a opne-time use code
        //return back to response
        $request->validate([
            //'name' => 'required|string',
            'phone' => 'required|numeric|min:10'
        ]);

        //create the model
        $user = User::firstOrCreate([
            //'name' => $request->name,
            'phone' => $request->phone
        ]);

        if (!$user)
        {
            return response()->json(['message' => 'Could not process a user with that phone number.'], 401);
        }

        //send user a opne-time use code
        //notify() expects a notification class to be passed in
        $user->notify(new LoginNeedsVerification());

        //return a response
        return response()->json(['message' => 'Text message notification sent.']);
        
    }


    public function verify(Request $request)
    {
        //validate incoming request
        //find user
        //does code provided match the code saved earlier?
        //if so return an auth token
        //if not return an error message

        //validate incoming request
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' =>'required|numeric|between:111111, 999999'
        ]);

        //find user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        //does code provided match the code saved earlier?

        //if so return an auth token
        if ($user)
        {
            //set the login_code to null so it can't be used again
            $user->update([
                'login_code' => null
            ]);
            //we can call 'createToken()' on the user because we are using the Sanctum package
            //& though we can pass a randon name (string) for the token name, we decided to use 
            //the login code as the name below. 
            return $user->createToken($request->login_code)->plainTextToken;
        }

        //if not return an error message
        return response()->json(['message' => 'Invalid verification code.'], 401);
        
    }
}
