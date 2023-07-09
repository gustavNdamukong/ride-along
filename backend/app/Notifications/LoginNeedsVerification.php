<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;


class LoginNeedsVerification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        //Here are the channels to be used for messages, by default we have here 'mail'. Modify it as you please.
        //For every channel in this 'via' method; create a method just like 'toMail()' below that will use it to
        //fire off the actual notification 
        return [TwilioChannel::class];
        /////return ['mail'];
    }

    /**
     * When in LoginController's submit() method we called $user->notify(new LoginNeedsVerification())
     * this toTwilio() method is called here, with the User object being the $notifiable argument passed in
     * @param $notifiable the object to be notified eg a User model
     */
    public function toTwilio($notifiable)
    {
        $loginCode = rand(111111, 999999);

        $notifiable->update([
            'Login_code' => $loginCode
        ]);

        return (new TwilioSmsMessage())
            ->content("Your Ride-along login code is {$loginCode}, dont share this with anyone!"); 
    }


    //---------------------------------
    

    
    //---------------------------------

    /**
     * Get the mail representation of the notification.
     */
    /*public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }*/

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
