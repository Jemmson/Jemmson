<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Task;
use App\User;

class TaskWasNotApproved extends Notification
{
    use Queueable;
    protected $task, $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, User $user)
    {
        $this->task = $task;
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
        if ($this->task->contractor_id !== $this->user->id) {
            return (new MailMessage)
                    ->line('Task was not approved. Sub Contractor.')
                    ->action('View Job', url('/login/sub/task/' . $this->task->id . '/' . $this->user->generateToken(true)->token))
                    ->line('Thank you for using our application!');
        }

        return (new MailMessage)
                    ->line('Task was not approved. General Contractor.')
                    ->action('View Job', url('/login/contractor/' . $this->task->job_id . '/' . $this->user->generateToken(true)->token))
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
