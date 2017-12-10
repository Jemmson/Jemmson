<?php

namespace App\Notifications;

use App\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifySubOfTaskToBid extends Notification
{

    protected $taskId;
    protected $user;
    protected $token;
    protected $userExists;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($taskId, $user, $token, $userExists)
    {

        $this->taskId = $taskId;
        $this->user = $user;
        $this->token = $token;
        $this->userExists = $userExists;
    }

//    public function __construct()
//    {

//        $this->task = $task;
//        $this->user = $user;
//        $this->token = $token;
//    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->userExists) {
            return (new MailMessage)
                ->line('Welcome ' . $this->user->name . ' back to Jemmson.')
                ->line('Please Sign In and go bid on your task')
                ->action('Notification Action', url('/' . $this->taskId))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->line('Welcome ' . $this->user->name . ' to Jemmson.')
                ->line('Please follow these steps to sign up for the site. and review your task.')
                ->action('Notification Action', url('/'))
                ->line('Thank you for using our application!');
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
