<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ListTaskCommentAction extends ParentAction
{
    public function run(Request $request)
    {
        $task = app(FindTaskWithProjectCheckTask::class)->run(
            $request->task_id,
            $request->project_id,
            $request->workspace_id,
            $request->user()->id,
            ['comments']
        );
        $comments = $task->comments()
            ->with('user')
            ->whereNull('parent_id')
            ->with('replies.user')
            ->latest()
            ->paginate(20);
        return $comments;
    }
}
