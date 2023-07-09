<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    //allow for mass-assignment
    protected $guarded = [];

    //pre-convert table field datatypes before they are inserted into the DB
    //this saves you having to modify your migrations, & re-running migrations
    //In this case, an API submission is passing in a json object (string) whilst the DB fields expect a json object or an array
    //(see the trips migration file for the datatypes in the up() method). This might sound confusing, but incoming requests into
    //the application will be json strings, but they will be serialized & converted into json objects before storing in the DB.
    //Hence we pre-convert (cast) the submitted data for these DB fields requiring arrays to arrays before trying to save them
    protected $casts = [
        'is_started' => 'boolean',
        'is_complete' => 'boolean',
        'origin' => 'array',
        'destination' => 'array',
        'destination_name' => 'array',
        'driver_location' => 'array',
    ];


    /**
     * A driver can drive multiple users on this app. 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Just like a user, a driver has many trips associated with them on this app
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
