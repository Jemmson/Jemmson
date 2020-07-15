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


class TaskWasNotApproved extends Notification implements ShouldQueue
{
    use Queueable;
    protected $task, $user, $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobTask $task, User $user, string $message = null)
    {
        $this->task = $task;
        $this->user = $user;
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
        if ($this->task->contractor_id !== $this->user->id) {
            return (new MailMessage)
                    ->line('Your finished task was not approved.')
                    ->line($this->message)
                    ->action('View Job ', url('/login/sub/task/' .
                        $this->task->id . '/' .
                        $this->user->generateToken(
                            $this->user->id,
                            true,
                            $this->task->id,
                            'approved',
                            'approved_by_customer',
                            'finished_job_denied_by_contractor',
                            'email'
                        )->token, [], true))
                    ->line('Thank you for using our application!');
        }

        return (new MailMessage)
                    ->line('Your finished task was not approved.')
                    ->line($this->message)
                    ->action('View Job ', url('/login/contractor/' .
                        $this->task->job_id . '/' .
                        $this->user->generateToken(
                            $this->user->id,
                            true,
                            $this->task->id,
                            'approved',
                            'customer_changes_finished_task',
                            'customer_changes_finished_task',
                            'email'
                        )->token, [], true))
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

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param mixed $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {

        if ($this->task->contractor_id !== $this->user->id) {

            $url = url('/login/sub/task/'. $this->task->id . '/' .
                $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->task->id,
                    'approved',
                    'approved_by_customer',
                    'finished_job_denied_by_contractor',
                    'text'
                )->token, [], true);

            $text = 'Your finished task was not approved. '. $this->message . ' ' .
                $url;
        } else {

            $url = url('/login/contractor/' . $this->task->id . '/' .
                $this->user->generateToken(
                    $this->user->id,
                    true,
                    $this->task->id,
                    'approved',
                    'customer_changes_finished_task',
                    'customer_changes_finished_task',
                    'text'
                )->token, [], true);

            $text = 'Your finished task was not approved. '. $this->message . ' '.
                $url;
        }

        NotificationLog::info('TaskWasNotApproved Notification Message: ' . $text);
        NotificationLog::info('TaskWasNotApproved Notification Link: ' . $url);

        return (new NexmoMessage)
            ->content($text);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'task' => $this->task,
        ]);
    }
}

