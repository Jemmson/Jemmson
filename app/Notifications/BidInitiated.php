<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


use App\User;
use Log;

class BidInitiated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user, $contractor;
    public $job;
    public $emailToken;
    public $textToken;
    public $value = 10;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job, $user)
    {
        $this->job = $job;
        $this->user = $user;

        $this->emailToken = $this->user->generateToken(
            $this->user->id,
            true,
            $this->job->id,
            'initiated',
            null,
            null,
            'email'
        )->token;

        $this->textToken = $this->user->generateToken(
            $this->user->id,
            true,
            $this->job->id,
            'initiated',
            null,
            null,
            'text'
        )->token;

        $this->contractor = $job->contractor()->first()->name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'nexmo'];
    }

    public function toDatabase($notifiable)
    {
        return [
          'bid_status' => 'Bid Initiated',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A bid has been initiated by contractor: ' . $this->contractor)
            ->action('Login', url('/login/customer/' . $this->job->id . '/' .
                $this->emailToken))
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
        $text = 'Welcome to Jemmson ' .
            $this->contractor .
            ' has initated a bid ' .
            '- Job Name: ' .
            $this->job->job_name .
            ' The link below will expire in one week.' .
            ' Login Link: ' .
            url('/login/' .
                'customer/' .
                $this->job->id .
                '/' .
               $this->textToken);
        return (new NexmoMessage)
            ->content($text);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'job' => $this->job,
        ]);
    }
}