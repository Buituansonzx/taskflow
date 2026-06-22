<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Containers\ClientSection\Task\UI\API\Requests\UpdateTaskRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class UpdateTaskAction extends ParentAction
{
    public function run(UpdateTaskRequest $request)
    {
        $data = $request->validated();
        $hasAssignees = array_key_exists('assignees', $data);
        $hasTags = array_key_exists('tags', $data);
        $assignees = $data['assignees'] ?? [];
        $tags = $data['tags'] ?? [];
        
        $task = app(FindTaskWithProjectCheckTask::class)->run(
            $request->task_id,
            $request->project_id,
            $request->workspace_id,
            $request->user()->id,
            ['assignees', 'tags']
        );
        unset($data['assignees'], $data['tags']);

        DB::transaction(function () use (
            $task,
            $data,
            $hasAssignees,
            $hasTags,
            $assignees,
            $tags
        ) {
            $task->update($data);

            if ($hasAssignees) {
                $task->assignees()->sync($assignees);
            }

            if ($hasTags) {
                $task->tags()->sync($tags);
            }
        });

        return $task->fresh(['assignees', 'tags','createdBy','subTasks','subTasks.assignees','subTasks.tags']);
    }
}
