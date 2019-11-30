<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


class NotifySubOfAcceptedBid extends Notification implements ShouldQueue
{
    use Queueable;
    protected $bid;
    protected $user;

    /**
     * Construct
     *
     * @param Task $bid
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
        return ['mail', 'broadcast', 'nexmo'];
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
                    ->line('Your bid for ' . $this->bid->name . ' has been accepted')
                    ->action('View Job',
                        url('/login/sub/task/'. $this->bid->id . '/'
                            . $this->user->generateToken(
                                true,
                                $this->bid->id,
                                'in_progress',
                                'initiated',
                                'accepted',
                                'email'
                            )->token))
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
     * @param mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {

        $text = $this->user->name . " has approved your bid " .
            url('/login/sub/task/'. $this->bid->id . '/' .
                $this->user->generateToken($this->user->id,
                    true,
                    $this->bid->id,
                    'in_progress',
                    'initiated',
                    'accepted',
                    'text')->token);

        return (new NexmoMessage)
            ->content($text);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'bid' => $this->bid,
        ]);
    }
}

