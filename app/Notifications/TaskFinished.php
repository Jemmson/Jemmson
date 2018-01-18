<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Task;

class TaskFinished extends Notification
{
    use Queueable;
    protected $task;
    protected $customer;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, Bool $customer)
    {
        $this->task = $task;
        $this->customer = $customer;
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
                    ->action('View Task', url('/task/' . $this->task->id))
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
