<?php

namespace App\Observers;

use App\Containers\ClientSection\Task\Models\Task;
use App\Containers\ClientSection\Task\Models\TaskActivity;

class TaskObserver
{
    private const TRACK_FIELDS = ['status', 'priority', 'deadline'];
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        TaskActivity::create([
            'task_id'    => $task->id,
            'project_id' => $task->project_id,
            'actor_id'   => auth()->id(),
            'action'     => TaskActivity::ACTION_CREATED,
            'old_value'  => null,
            'new_value'  => null,
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $changes  = $task->getChanges();
        $original = $task->getOriginal();

        foreach (self::TRACK_FIELDS as $field) {
            if (array_key_exists($field, $changes)) {
                TaskActivity::create([
                    'task_id'    => $task->id,
                    'project_id' => $task->project_id,
                    'actor_id'   => auth()->id(),
                    'action'     => "{$field}_changed",
                    'old_value'  => [$field => $original[$field]],
                    'new_value'  => [$field => $changes[$field]],
                ]);
            }
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        TaskActivity::create([
            'task_id'    => $task->id,
            'project_id' => $task->project_id,
            'actor_id'   => auth()->id(),
            'action'     => TaskActivity::ACTION_DELETED,
            'old_value'  => null,
            'new_value'  => null,
        ]);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
