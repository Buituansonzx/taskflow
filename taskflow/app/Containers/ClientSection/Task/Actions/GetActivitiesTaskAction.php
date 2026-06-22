<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class GetActivitiesTaskAction extends ParentAction
{
    public function run(Request $request)
    {
        $task = app(FindTaskWithProjectCheckTask::class)->run(
            $request->task_id,
            $request->project_id,
            $request->workspace_id,
            $request->user()->id
        );
        $activities = TaskActivity::with('actor')->where('task_id', $task->id)->orderBy('created_at', 'asc')->paginate(10);
        return $activities;
    }
}
