<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

use App\Job;
use App\User;

class JobBidDeclined extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bid, $user, $message, $customer;

    /**
     * Create a new notification instance.
     *
     * @param Job $bid
     * @param User $user
     * @param string $message
     * @param User $customer
     */
    public function __construct(Job $bid, User $user, string $message = null, User $customer)
    {
        $this->bid = $bid;
        $this->user = $user;
        $this->message = $message;
        $this->customer = $customer;

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
                    ->line('Customer is Requesting a Change.')
                    ->line($this->message)
                    ->action('View Job ', url('/login/contractor/' .
                        $this->bid->id . '/' .
                        $this->user->generateToken(
                            $this->user->id,
                            true,
                            $this->bid->id,
                            'changed',
                            'waiting_for_customer_approval',
                            'waiting_for_customer_approval',
                            'email'
                        )->token, [], true))
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

        $text = $this->customer->name . " requests a change to your bid. The customer's message is: "
            . $this->bid->declined_message . ".  Please go to the link below to view the job. "
            . url('/login/contractor/' . $this->bid->id . '/'
                . $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->bid->id,
                    'changed',
                    'waiting_for_customer_approval',
                    'waiting_for_customer_approval',
                    'text'
                )->token, [], true);

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

