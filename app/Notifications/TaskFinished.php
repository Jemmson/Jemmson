<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

use App\Task;
use App\User;

class TaskFinished extends Notification implements ShouldQueue
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
        if ($this->customer) {
            $custom = "Please approve the task \n";
            $generalStatus = 'general_finished_work';
            $subStatus = '';
        } else {
            $custom = "Please review the finished task. ";
            $generalStatus = 'sub_finished_work';
            $subStatus = 'finished_job';
        }
        return (new MailMessage)
                    ->line("The task: " . $this->task->name . " has been finished. ")
                    ->line($custom)
                    ->action(' View Task ', url('/login/mix/' .
                        $this->task->jobTask()->first()->job_id . '/' .
                        $this->user->generateToken(
                            $this->user->id,
                            true,
                            $this->task->id,
                            'approved',
                            $generalStatus,
                            $subStatus,
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
        if ($this->customer) {
            $custom = "Please approve the task. \n";
            $generalStatus = 'general_finished_work ';
            $subStatus = '';
        } else {
            $custom = "Please review the finished task. ";
            $generalStatus = 'sub_finished_work ';
            $subStatus = 'finished_job ';
        }
        $text = "The task: " . $this->task->name . " has been finished. "
            . $custom
            . "' View Task '\n"
            . url('/login/mix/' .
                $this->task->jobTask()->first()->job_id . '/' .
                $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->task->id,
                    'approved',
                    $generalStatus,
                    $subStatus,
                    'text'
                )->token, [], true);

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
            'task' => $this->task,
        ]);
    }
}

