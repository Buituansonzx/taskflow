<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Task\Models\Comment;
use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Containers\ClientSection\Task\UI\API\Requests\CreateTaskCommentRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class CreateTaskCommentAction extends ParentAction
{
    public function run(CreateTaskCommentRequest $request)
    {
        $task = app(FindTaskWithProjectCheckTask::class)->run(
            $request->task_id,
            $request->project_id,
            $request->workspace_id,
            $request->user()->id
        );
        $data = $request->validated();
        if(isset($data['parent_id'])){
            $parent = Comment::find($data['parent_id']);
            if($parent->parent_id !== null){
                throw new HttpException(422, 'Không thể reply vào reply');
            }
        }
        $comment = Comment::create([
            'task_id'   => $task->id,
            'user_id'   => $request->user()->id,
            'content'   => $data['content'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);
        return $comment;
    }
}
