<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Auth;


class NotifyCustomerThatBidIsFinished extends Notification implements ShouldQueue
{
    use Queueable;
    protected $bid, $user, $company_name;

    /**
     * Construct
     *
     * @param Job $bid
     * @param User $user
     * @param String $companyName
     *
     */
    public function __construct($bid, $user, String $companyName)
    {
        $this->bid = $bid;
        $this->user = $user;
        $this->company_name = $companyName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'broadcast', 'nexmo'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
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
     * @param mixed $notifiable
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

        $text = $this->company_name . " has finished your bid. Please go to the link below to take action on the job " . url('/login/customer/' . $this->bid->id . '/' . $this->user->generateToken(true)->token);

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

