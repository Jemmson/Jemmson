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
use Illuminate\Support\Facades\Log;

class TaskFinished extends Notification implements ShouldQueue
{
    use Queueable;
    protected
        $task,
        $customer,
        $sub,
        $job,
        $general,
        $jobTask;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        $task,
        $customer,
        $sub,
        $general,
        $jobTask,
        $job
    )
    {
        $this->task = $task;
        $this->customer = $customer;
        $this->sub = $sub;
        $this->general = $general;
        $this->job = $job;
        $this->jobTask = $jobTask;
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
        if ($this->customer) {
            $custom = "Please approve the task. \n";
            $generalStatus = 'general_finished_work';
            $subStatus = '';
            $user = $this->customer;
        } else {
            $custom = "Please review the finished task. ";
            $generalStatus = 'sub_finished_work';
            $subStatus = 'finished_job';
            $user = $this->general;
        }
        return (new MailMessage)
            ->line("The task: " . $this->task->name . " has been finished. ")
            ->line($custom)
            ->line(" View Task: ")
            ->action("Task: ", url('/login/mix/' .
                $this->job->id . '/' .
                $user->generateToken(
                    $user->id,
                    true,
                    $this->jobTask->name,
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
            $custom = "General: " . $this->general->name . "\n
        Job: " . $this->job->name . "\n
        Task: " . $this->task->name . "\n";
            $generalStatus = 'general_finished_work ';
            $subStatus = '';
            $user = $this->customer;
        } else {
            $custom = "Sub: " . $this->sub->name . "\n
        Job: " . $this->job->name . "\n
        Task: " . $this->task->name . "\n";
            $generalStatus = "sub_finished_work ";
            $subStatus = "finished_job ";
            $user = $this->customer;
        }
        $text = "The task: " . $this->task->name . " has been finished. "
            . $custom
            . " View Task: \n"
            . url('/login/mix/' .
                $this->job->id . '/' .
                $user->generateToken(
                    $user->id,
                    true,
                    $this->jobTask->name,
                    'approved',
                    $generalStatus,
                    $subStatus,
                    'text'
                )->token, [], true);

//        Log::debug((new NexmoMessage)
//            ->content($text));

        return (new NexmoMessage)
            ->content($text);
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

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'task' => $this->jobTask,
        ]);
    }
}

