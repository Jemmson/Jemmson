<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Auth;

use App\User;
use App\Job;

class NotifyContractorOfSubBid extends Notification implements ShouldQueue
{
    use Queueable;
    protected $user, $subName, $bid;

    /**
     * Create a new notification instance.
     *
     * @param Job $bid
     * @param string $subName
     * @param User $user
     */
    public function __construct(Job $bid, $subName, User $user)
    {
        $this->user = $user;
        $this->subName = $subName;
        $this->bid = $bid;
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
                    ->line('Hello ' . $this->user->name . ' Contractor ' . $this->subName)
                    ->line('Has just submitted a bid for the task you sent him.')
                    ->action('View Bid', url('/login/contractor/' . $this->bid->id . '/' . $this->user->generateToken(true)->token))
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
     *
     */
    public function toNexmo($notifiable)
    {

        $text = 'Hello ' . $this->user->name . ' Contractor ' . $this->subName . ' has just submitted a bid for the task you sent him. Please use the following link. ' . url('/login/contractor/' . $this->bid->id . '/' . $this->user->generateToken(true)->token);

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

