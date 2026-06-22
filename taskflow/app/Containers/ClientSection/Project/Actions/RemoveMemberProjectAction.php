<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Project\UI\API\Requests\RemoveMemberProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RemoveMemberProjectAction extends ParentAction
{
    public function run(RemoveMemberProjectRequest $request)
    {
        $projectId = $request->project_id;
        $workspaceId = $request->workspace_id;
        $project = Project::with('members')->where('id', $projectId)->where('workspace_id', $workspaceId)->firstOrFail();
        $user = User::where('email', $request->email)->first();

        $existsInProject = $project->members()
            ->wherePivot('user_id', $user->id)
            ->exists();

        if (!$existsInProject) {
            throw new HttpException(404, 'Người dùng không phải thành viên của project này');
        }

       $project->members()->detach($user->id);
    }
}
