<?php

namespace App\Notifications;

use App\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Support\Facades\Log;
use Laravel\Spark\Notifications\SparkChannel;
use Laravel\Spark\Notifications\SparkNotification;
use App\JobTask;

class NotifySubOfTaskToBid extends Notification implements ShouldQueue
{

    protected $jobTaskId;
    protected $user;
    protected $emailToken;
    protected $nexmoToken;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobTaskId, $user, $jobId)
    {
        $this->jobTaskId = $jobTaskId;
        $this->user = $user;
        $this->createEmailToken($jobId, $jobTaskId, $user);
        $this->createNexmoToken($jobId, $jobTaskId, $user);

    }

    private function createEmailToken($jobId, $jobTaskId, $user)
    {
        $this->emailToken = $user->generateToken(
            $this->user->id,
            true,
            $jobId,
            'in_progress',
            'initiated',
            'initiated',
            'email',
            $jobTaskId
        )->token;
    }

    private function createNexmoToken($jobId, $jobTaskId, $user)
    {
        $this->nexmoToken = $this->user->generateToken(
            $user->id,
            true,
            $jobId,
            'in_progress',
            'initiated',
            'initiated',
            'text',
            $jobTaskId
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
        $jobTask = JobTask::find($this->jobTaskId);
        if (true) {
            return (new MailMessage)
                ->line('Welcome ' . $this->user->name . ' back to Jemmson.')
                ->line('You have a potential job! Please sign in to see it. ')
                ->action('Login ', url('/login/sub/task/'. $jobTask->id . '/' . $this->emailToken, [], true))
                ->line('Thank you for using our application!');;
        } else {
            return (new MailMessage)
                ->line('Welcome ' . $this->user->name . ' to Jemmson.')
                ->line('Please follow these steps to sign up for the site. and review your task.')
                ->action('Login ', url('/login/sub/task/'.
                    $jobTask->id . '/' .
                    $this->emailToken, [], true))
                ->line('Thank you for using our application!');;
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
            ->content('You have a potential job! Please sign in to see it. ' .
                url('/login/sub/task/'.
                    $this->jobTaskId . '/' . $this->nexmoToken, [], true) . ' ');;
    }

    public function toSpark($notifiable)
    {
        return (new SparkNotification)
                      ->action('View Task', '/bid/tasks?taskId=' . $this->jobTaskId)
                      ->icon('fa-users')
                      ->body('A contractor sent you a task!');
    }

    public function toBroadcast($notifiable)
    {
        $jt = JobTask::find($this->jobTaskId);
        $task = Task::find($jt->task_id);
        return new BroadcastMessage([
            'task' => $task,
        ]);
    }
}