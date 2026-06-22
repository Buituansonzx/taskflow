<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Task;
use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FindTaskByIdAction extends ParentAction
{
    public function run(Request $request)
    {
        $task = app(FindTaskWithProjectCheckTask::class)->run(
                $request->task_id,
                $request->project_id,
                $request->workspace_id,
                $request->user()->id,
                [
                    'assignees',
                    'tags',
                    'subTasks',
                    'subTasks.assignees',
                    'subTasks.tags',
                    'createdBy',
                ]
            );
        return $task;
    }
}
