<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Driver;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            //A trip is related to both users & drivers, hence...
            $table->foreignIdFor(User::class);
            //make this nullable coz a trip will have a driver assoc with it until it is assigned to a driver
            $table->foreignIdFor(Driver::class)->nullable();
            //Whats the status of the trip (in progress, ended? etc ...we need to send notifications with these statuses as well)
            $table->boolean('is_started')->default(false);
            $table->boolean('is_complete')->default(false);
            //Now coordinates, these are stored as json objects 
            //we need vehicle location coordinates at all times
            $table->json('origin')->nullable();
            $table->json('destination')->nullable();
            //we need destinations' names to store & display in the app
            $table->json('destination_name')->nullable();
            //we need to be able to track & inform passengers (users) of their driver's location in response to their request for a ride
            //so they'll know how far away the driver is
            $table->json('driver_location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
