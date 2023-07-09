<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
      Notifiable allows us to be able to send all kinds of notifications from our app;
      like emails, slag, webhooks etc 
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*protected $fillable = [
        'name',
        'email',
        'password',
    ];*/
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        //'password',
        'login_code',
        'remember_token',
    ];

    //By default Twilion tries to find on this users table the phone number to send the notification to 
    //from a column 'phone_number'. We can create this field on the users table & update the users migration
    //file, or you can just create a method like 'routeNotificationForTwilio()' & make it return the user's 
    //phone number
    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    /**
     * A user can only have one driver. The person to pick them up
     */
    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * A user can make many trips on this app
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
