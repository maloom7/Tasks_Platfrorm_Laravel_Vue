<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable;
    public function __construct(public Task $task) {}
    public function via($notifiable) { return ['database']; }
    public function toDatabase($notifiable) {
        return [
            'type' => 'task_assigned',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'by' => auth()->user()->only('id','name')
        ];
    }
}