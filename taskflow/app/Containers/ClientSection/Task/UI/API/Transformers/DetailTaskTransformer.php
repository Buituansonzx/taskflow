<?php

namespace App\Containers\ClientSection\Task\UI\API\Transformers;

use App\Containers\ClientSection\Task\Models\Task;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
final class DetailTaskTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [
        'subTasks'
    ];

    protected array $availableIncludes = [];

    public function transform(Task $task): array
    {
        return [
            'type' => $task->getResourceKey(),
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'created_by' => $task->createdBy->name,
            'priority' => $task->priority,
            'deadline' => $task->deadline,
            'estimated_hours' => $task->estimated_hours,
            'assignees' => $task->assignees->map(function ($assignee) {
                return [
                    'id' => $assignee->id,
                    'name' => $assignee->name,
                    'email' => $assignee->email,
                ];
            }),
            'tags' => $task->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ];
            }),
            'created_at' => $task->created_at->format('H:i - d-m-Y'),
            'updated_at' => $task->updated_at->format('H:i - d-m-Y'),
        ];
    }

    public function includeSubTasks(Task $task)
    {
        return $this->collection(
            $task->subTasks,
            new TaskTransformer()
        );
    }
}
