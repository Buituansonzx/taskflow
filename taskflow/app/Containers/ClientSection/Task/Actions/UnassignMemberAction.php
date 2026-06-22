<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Task;
use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Containers\ClientSection\Task\UI\API\Requests\UnassignMemberRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class UnassignMemberAction extends ParentAction
{
    public function run(UnassignMemberRequest $request)
    {
        $project = Project::with('members')->where('id', $request->project_id)->where('workspace_id', $request->workspace_id)->firstOrFail();
        $task = Task::with('assignees')->where('id', $request->task_id)->where('project_id', $project->id)->firstOrFail();
        
        $userIds = $request->users;
        foreach ($userIds as $userId) {
            $isProjectMember = $project->members()->where('user_id', $userId)->exists();
            if (!$isProjectMember) {
                throw new HttpException(422, "User {$userId} không phải thành viên của project");
            }

            $isAssigned = $task->assignees()->where('user_id', $userId)->exists();
            if (!$isAssigned) {
                throw new HttpException(422, "User {$userId} chưa được assign vào task này");
            }
        }
        $task->assignees()->detach($userIds);
        TaskActivity::create([
            'task_id'    => $task->id,
            'project_id' => $task->project_id,
            'actor_id'   => auth()->id(),
            'action'     => TaskActivity::ACTION_ASSIGNEE_REMOVED,
            'old_value'  => ['user_ids' => $userIds, 'users' => User::whereIn('id', $userIds)->pluck('name', 'id')],  
            'new_value'  => null,
        ]);
        return $task;
    }
}
