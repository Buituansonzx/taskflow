<?php

namespace App\Containers\ClientSection\Task\UI\API\Transformers;

use App\Containers\ClientSection\Task\Models\Task;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TaskTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Task $task): array
    {
        return [
            'type'           => $task->getResourceKey(),
            'id'             => $task->id,
            'title'          => $task->title,
            'priority'       => $task->priority,
            'status'         => $task->status,
            'deadline'       => $task->deadline,
            'tags'           => $task->tags->map(fn($tag) => [
                'id'    => $tag->id,
                'name'  => $tag->name,
                'color' => $tag->color,
            ]),
            'assignees'      => $task->assignees->map(fn($assignee) => [
                'id'    => $assignee->id,
                'name'  => $assignee->name,
            ]),
            'sub_tasks_count' => $task->subTasks->count(),
            'created_at'     => $task->created_at->format('H:i d-m-Y'),
        ];
    }
}
