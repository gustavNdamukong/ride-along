<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    //allow for mass-assignment
    protected $guarded = [];


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
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
