<?php

namespace App\Notifications;

use App\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Laravel\Spark\Notifications\SparkChannel;
use Laravel\Spark\Notifications\SparkNotification;

class NotifySubOfTaskToBid extends Notification implements ShouldQueue
{

    protected $taskId;
    protected $user;
    protected $emailToken;
    protected $nexmoToken;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($taskId, $user)
    {
        $this->taskId = $taskId;
        $this->user = $user;
        $this->createEmailToken($taskId, $user);
        $this->createNexmoToken($taskId, $user);

    }

    private function createEmailToken($taskId, $user)
    {
        $this->emailToken = $user->generateToken(
            $this->user->id,
            true,
            $taskId,
            'in_progress',
            'initiated',
            'initiated',
            'email'
        )->token;
    }

    private function createNexmoToken($taskId, $user)
    {
        $this->nexmoToken = $this->user->generateToken(
            $user->id,
            true,
            $taskId,
            'in_progress',
            'initiated',
            'initiated',
            'text'
        )->token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SparkChannel::class, 'mail', 'broadcast', 'nexmo'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $task = Task::find($this->taskId);
        if (true) {
            return (new MailMessage)
                ->line('Welcome ' . $this->user->name . ' back to Jemmson.')
                ->line('Please Sign In and go bid on your task')
                ->action('Login', url('/login/sub/task/'. $task->id . '/' . $this->emailToken))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->line('Welcome ' . $this->user->name . ' to Jemmson.')
                ->line('Please follow these steps to sign up for the site. and review your task.')
                ->action('Login', url('/login/sub/task/'.
                    $task->id . '/' .
                    $this->emailToken))
                ->line('Thank you for using our application!');
        }

    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content('Please Sign In and go bid on your task ' .
                        url('/login/sub/task/'.
                            $this->taskId . '/' . $this->nexmoToken) . ' ');
    }

    public function toSpark($notifiable)
    {
        return (new SparkNotification)
                      ->action('View Task', '/bid/tasks?taskId=' . $this->taskId)
                      ->icon('fa-users')
                      ->body('A contractor sent you a task!');
    }

    public function toBroadcast($notifiable)
    {
        $task = Task::find($this->taskId);
        return new BroadcastMessage([
            'task' => $task,
        ]);
    }
}