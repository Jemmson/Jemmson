<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Task;
use App\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class TaskFinished extends Notification implements ShouldBroadcast
{
    use Queueable;
    protected $task, $customer, $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, Bool $customer, User $user)
    {
        $this->task = $task;
        $this->customer = $customer;
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
        $custom = '';
        if ($this->customer) {
            $custom = "Please approve the task";
        } else {
            $custom = "Please review the finished task.";
        }
        return (new MailMessage)
                    ->line("The task: " . $this->task->name . " has been finished.")
                    ->line($custom)
                    ->action('View Task', url('/login/mix/' . $this->task->jobTask()->first()->job_id . '/' . $this->user->generateToken(true)->token))
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
            'message' => "The task: " . $this->task->name . " has been finished.",
        ]);
    }
}

