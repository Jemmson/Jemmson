<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


use App\User;

class BidInitiated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user, $pwLink, $contractor;
    public $job;
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
        $this->pwLink = $this->user->generateToken(true)->token;
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
        $notifyThrough = ['broadcast'];

        if ($notifiable->phone) {
            array_push($notifyThrough, 'nexmo');
        }

//        if ($notifiable->email) {
//            array_push($notifyThrough, 'email', 'broadcast');
//        }

        return $notifyThrough;
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
            ->action('Login', url('/login/customer/' . $this->job->id . '/' . $this->pwLink))
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
        $text = 'Welcome To Jemmson ' .
            $this->contractor .
            ' has initated a bid ' .
            '- Job Name: ' .
            $this->job->job_name .
            ' The link below will expire in one hour.' .
            ' Login Link: ' .
            url('/login/' .
                'customer/' .
                $this->job->id .
                '/' .
                $this->pwLink);
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