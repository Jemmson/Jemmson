<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class NotifyJobHasBeenApproved extends Notification implements ShouldBroadcast
{
    use Queueable;
    protected $job, $user;
    protected $sub = false;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job, $user)
    {
        $this->job = $job;
        $this->user = $user;
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
                    ->action('View Job', url('/login/sub/task/' . $this->job->id . '/' . $this->user->generateToken(true)->token))
                    ->line('Thank you for using our application!');
        }

        return (new MailMessage)
                    ->line('Job has been approved. General Contractor.')
                    ->action('View Job', url('/login/contractor/' . $this->job->id . '/' . $this->user->generateToken(true)->token))
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

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Job has been approved. Sub Contractor.',
        ]);
    }
}

