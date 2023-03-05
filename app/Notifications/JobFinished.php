<?php

namespace App\Notifications;

use App\Traits\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Log;


use App\User;
use App\Job;


class JobFinished extends Notification implements ShouldQueue
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
    public function __construct($bid, $user, string $companyName)
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
        $url = url('/login/customer/' . $this->bid->id . '/' .
            $this->user->generateToken(
                $this->user->id,
                true,
                $this->bid->id,
                'sent',
                'finished',
                'not set',
                'text')->token, [], true);

        $text = $this->company_name . " has finished you job. Please pay for the job using the link below ";

        NotificationLog::info('NotifyCustomerThatBidIsFinished Notification Message: ' . $text);
        NotificationLog::info('NotifyCustomerThatBidIsFinished Notification Link: ' . $url);


        return (new MailMessage)
            ->line($text)
            ->action('', url($url))
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
        $url = url('/login/customer/' . $this->bid->id . '/' .
            $this->user->generateToken(
                $this->user->id,
                true,
                $this->bid->id,
                'sent',
                'finished',
                'not set',
                'text')->token, [], true);

        $text = "Jemmson: " . $this->company_name . " has finished your job. Please pay for the job using the link below " . $url;

        NotificationLog::info('NotifyCustomerThatBidIsFinished Notification Message: ' . $text);
        NotificationLog::info('NotifyCustomerThatBidIsFinished Notification Link: ' . $url);


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

