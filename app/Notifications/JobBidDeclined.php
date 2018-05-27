<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


use App\Job;
use App\User;

class JobBidDeclined extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $bid, $user;

    /**
     * Create a new notification instance.
     *
     * @param Job $bid
     * @param User $user
     * @param string $message
     */
    public function __construct(Job $bid, User $user, string $message = null)
    {
        $this->bid = $bid;
        $this->user = $user;
        $this->message = $message;
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
                    ->line('Job was declined.')
                    ->line($this->message)
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

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Job was declined.',
        ]);
    }
}

