<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


class NotifyCustomerThatBidIsFinished extends Notification implements ShouldQueue
{
    use Queueable;
    protected $bid, $user;
    /**
     * Construct
     *
     * @param Job $bid
     * @param User $user
     */
    public function __construct($bid, $user)
    {
        $this->bid = $bid;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/login/customer/' . $this->bid->id . '/' . $this->user->generateToken(true)->token))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {

        $text = "$this->user has finished your bid. 
            Please log into Jemmson.com to take a look at 
            your bid.";

//        $text = 'Welcome To Jemmson ' .
//            $this->contractor .
//            ' has initated a bid ' .
//            '- Job Name: ' .
//            $this->bid->job_name .
//            ' The link below will expire in one week.' .
//            ' Login Link: ' .
//            url('/login/' .
//                'customer/' .
//                $this->bid->id .
//                '/' .
//                $this->pwLink);
        return (new NexmoMessage)
            ->content($text);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'job' => $this->bid,
        ]);
    }
}

