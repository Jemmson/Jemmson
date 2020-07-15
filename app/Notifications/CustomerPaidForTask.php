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

class CustomerPaidForTask extends Notification implements ShouldQueue
{
    use Queueable;
    protected $task, $user;

    /**
     * Create a new notification instance.
     *
     * @param Task $task
     * @param User $user
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
        $isGeneral = $this->task->contractor_id === $this->user->id;

        if ($isGeneral) {
            return (new MailMessage)
                    ->line('Customer has sent you a payment for : '. $this->task->name . '.')
                    ->action('View Task ',
                        url('/login/general/' . $this->task->jobTask()->first()->job_id . '/'
                            .  $this->user->generateToken(
                                $this->user->id,
                                true,
                                $this->task->id,
                                'paid',
                                'paid',
                                'paid',
                                'email'
                            )->token
                            , [], true))
                    ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                    ->line('Customer has sent you a payment for : ' .$this->task->name . '.')
                    ->action('View Task ',
                        url('/login/sub/task/' . $this->task->id . '/'
                            . $this->user->generateToken(
                                $this->user->id,
                                true,
                                $this->task->id,
                                'paid',
                                'paid',
                                'paid',
                                'email'
                            )->token, [], true))
                    ->line('Thank you for using our application!');
        }

    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        $isGeneral = $this->task->contractor_id === $this->user->id;

        if ($isGeneral) {

            $url = url('/login/general/' . $this->task->id . '/'
                . $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->task->id,
                    'paid',
                    'paid',
                    'paid',
                    'text'
                )->token, [], true);


            $text = 'Customer has sent you a payment for : '. $this->task->name . '. '
                . $url
            . ' Thank you for using our application!';
        } else {

            $url = url('/login/sub/task/' . $this->task->id . '/'
                . $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->task->id,
                    'paid',
                    'paid',
                    'paid',
                    'text'
                )->token, [], true);

            $text = 'Customer has sent you a payment for : '. $this->task->name . '. '
                . $url
            . ' Thank you for using our application!';
        }

        Log::info('CustomerPaidForTask Notification Message: ' . $text);
        Log::info('CustomerPaidForTask Notification Link: ' . $url);

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
