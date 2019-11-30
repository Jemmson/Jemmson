<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


class NotifyJobHasBeenApproved extends Notification implements ShouldQueue
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
        if ($this->sub) {
            return (new MailMessage)
                ->line('The Customer Has Approved The Job!')
                ->action('View Job', url('/login/sub/task/' . $this->job->id . '/' .
                    $this->user->generateToken(
                        $this->user->id,
                        true,
                        $this->job->id,
                        'approved',
                        'approved_by_customer',
                        'approved_by_customer',
                        'email')->token)
                )
                ->line('Thank you for using our application!');
        }

        return (new MailMessage)
            ->line('The Customer Has Approved The Job!')
            ->action('View Job', url('/login/contractor/' . $this->job->id . '/' .
                $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->job->id,
                    'approved',
                    'approved_by_customer',
                    'approved_by_customer',
                    'email'
                )->token)
            )
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

        if ($this->sub) {
            $text = "The customer has approved of the job. Please go to the link below view job and begin work. "
                . url('/login/sub/task/' . $this->job->id . '/'
                    . $this->user->generateToken(
                        $this->user->id,
                        true,
                        $this->job->id,
                        'approved',
                        'approved_by_customer',
                        'approved_by_customer',
                        'text'
                    )->token);
        } else {
            $text = "The customer has approved of the job. Please go to the link below view job and begin work. "
                . url('/login/contractor/' . $this->job->id . '/'
                    . $this->user->generateToken(
                        $this->user->id,
                        true,
                        $this->job->id,
                        'approved',
                        'approved_by_customer',
                        'approved_by_customer',
                        'text'
                    )->token);
        }

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

