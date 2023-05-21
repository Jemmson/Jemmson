<?php

namespace App\Notifications;

use App\Traits\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

use App\JobTask;
use App\User;


class TaskApproved extends Notification implements ShouldQueue
{
    use Queueable;
    protected $jobTask, $customer, $user, $task;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobTask $jobTask, User $user, Task $task)
    {
        $this->jobTask = $jobTask;
        $this->user = $user;
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'broadcast', 'nexmo'];
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
                    ->line("The task: " . $this->task->name . " has been Approved.")
                    ->line('Now waiting on customer to approve the task & send payment.')
                    ->action('View Task ', url('/login/sub/task/' .
                        $this->jobTask->id . '/' .
                        $this->user->generateToken(
                            $this->user->id,
                            true,
                            $this->jobTask->id,
                            'approved',
                            'approved_by_customer',
                            'finished_job_approved_by_contractor',
                            'email'
                        )->token, [], true))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {

        $url = url('/login/sub/task/' .
            $this->jobTask->id . '/' .
            $this->user->generateToken(
                $this->user->id,
                true,
                $this->jobTask->id,
                'approved',
                'approved_by_customer',
                'finished_job_approved_by_contractor',
                'text'
            )->token, [], true);

        $text = "The task: " . $this->task->name . " has been Approved."
            . ' Now waiting on customer to approve the task & send payment.'
            . ' View Task '
            . $url;

        NotificationLog::info('TaskApproved Notification Message: ' . $text);
        NotificationLog::info('TaskApproved Notification Link: ' . $url);


        return (new NexmoMessage)
            ->content($text);
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
            'task' => 'task approved',
        ]);
    }
}

