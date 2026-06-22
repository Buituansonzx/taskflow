<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class DeleteTaskAction extends ParentAction
{
    public function run(Request $request){
        $task = app(FindTaskWithProjectCheckTask::class)->run(
            $request->task_id,
            $request->project_id,
            $request->workspace_id,
            $request->user()->id,
            ['subTasks']
        );
        if($task->subTasks->isNotEmpty()){
            throw new HttpException(422, 'Không thể xóa task vì task này đã có subtask');
        }
        $task->delete();
    }
}
