<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyContractorOfAcceptedBid extends Notification
{
    use Queueable;
    protected $user, $bid;
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
        return ['mail'];
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
                    ->line('Your bid for job: ' . $bid->name)
                    ->lin('has been accepted')
                    ->action('View Job', url('/login/contractor/' . $this->bid->id . '/' . $this->user->generateToken(true)->token))
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
}
