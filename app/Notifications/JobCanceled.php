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

class JobCanceled extends Notification
{
    use Queueable;

    protected $jobName;

    /**
     * Create a new notification instance.
     *
     * @param Job $job
     * @param User $user
     * @param String $usertype
     *
     * @return void
     */
    public function __construct(String $jobName)
    {
        //
        $this->jobName = $jobName;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'nexmo'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action ', url('/', [], true))
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

        $text = $this->jobName . " has been canceled. Please login to see your bids. " . url('/#/bids', [], true);

        return (new NexmoMessage)
            ->content($text);

    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'job canceled',
        ]);
    }
}
