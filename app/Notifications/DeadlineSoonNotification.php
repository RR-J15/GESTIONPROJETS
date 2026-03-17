<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineSoonNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'La tâche "' . $this->task->title . '" arrive à échéance le ' . $this->task->due_date->format('d/m/Y'),
            'url'     => route('tasks.show', $this->task),
            'type'    => 'deadline_soon',
        ];
    }
}