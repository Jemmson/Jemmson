<?php

namespace App\Notifications;

use App\Task;
use App\TaskMessage;
use App\JobTask;
use App\Traits\NotificationLog;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\NexmoMessage;



class NotifyContractorThatCustomerChangesBid extends Notification
{
    use Queueable;
    protected $task, $customer, $contractor, $jobTask, $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        Task $task,
        User $contractor,
        User $customer,
        JobTask $jobTask,
        TaskMessage $message
    )
    {
        $this->task = $task;
        $this->contractor = $contractor;
        $this->customer = $customer;
        $this->jobTask = $jobTask;
        $this->message = $message;
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
                    ->line($this->customer->name . ' is requesting a change to your bid.');
    }


    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        $text = $this->customer->name . ' is requesting a change to your bid.';

        NotificationLog::info('NotifyContractorThatCustomerChangesBid Notification Message: ' . $text);
//        Log::info('NotifyContractorThatCustomerChangesBid Notification Link: ' . $url);


        return (new NexmoMessage)
            ->content($text);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
        ]);
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
