<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDue extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
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
        $task = $this->task;
        return (new MailMessage)
                    ->subject('Task Due Soon')
                    ->greeting("Hello!\nA task is due soon.")
                    ->line("Task: {$task->title}")
                    ->line("Due date: {$task->due_date->format('m/d/Y')}")
                    ->action('View Task', route('task.edit', $task))
                    ->line('Thank you for using our application!')
                    ->to($notifiable->email);
    }
}
