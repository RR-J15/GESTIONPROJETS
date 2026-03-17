<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
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
            'message' => 'Vous avez été assigné à la tâche "' . $this->task->title . '"',
            'url'     => route('tasks.show', $this->task),
            'type'    => 'task_assigned',
        ];
    }
}