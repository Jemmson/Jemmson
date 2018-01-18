<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyJobHasBeenApproved extends Notification
{
    use Queueable;
    protected $job;
    protected $sub = false;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job)
    {
        $this->job = $job;
    }

    /**
     * Set whether this user is a sub of the job
     *
     * @return void
     */
    public function setSub($sub)
    {
        $this->sub = $sub;
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
        if ($this->sub) {
            return (new MailMessage)
                    ->line('Job has been approved. Sub Contractor.')
                    ->action('View Job', url('/'))
                    ->line('Thank you for using our application!');
        }

        return (new MailMessage)
                    ->line('Job has been approved. General Contractor.')
                    ->action('View Job', url('/'))
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
}