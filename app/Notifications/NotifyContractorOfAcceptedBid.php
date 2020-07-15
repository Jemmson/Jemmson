<?php

namespace App\Notifications;

use App\Traits\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;



class NotifyContractorOfAcceptedBid extends Notification implements ShouldQueue
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
                    ->line('Your bid for job: ' . $this->bid->name)
                    ->line('has been accepted')
                    ->action('View Job ',
                        url('/login/contractor/' . $this->bid->id . '/'
                            . $this->user->generateToken(
                            $this->user->id,
                            true,
                            $this->bid->id,
                            'approved',
                            'approved_by_customer',
                            'approved_by_customer',
                            'email'
                        )->token, [], true))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        $url = url('/login/contractor/' . $this->bid->id . '/'
            . $this->user->generateToken(
                $this->user->id,
                true,
                $this->bid->id,
                'approved',
                'approved_by_customer',
                'approved_by_customer',
                'text'
            )->token, [], true);

        $text = 'Your bid for job: ' . $this->bid->name .
            'has been accepted' .
            ' View Job: ' .
            $url;

        NotificationLog::info('NotifyContractorOfAcceptedBid Notification Message: ' . $text);
        NotificationLog::info('NotifyContractorOfAcceptedBid Notification Link: ' . $url);

        return (new NexmoMessage)
            ->content($text);
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
            'job' => $this->bid,
        ]);
    }
}

