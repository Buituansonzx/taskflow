<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;

final class GetAvailableMembersAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->workspace_id;
        $projectId = $request->project_id;

        $project = Project::with('members', 'workspace')->where('id', $projectId)->where('workspace_id', $workspaceId)->firstOrFail();
        

        $projectMembers = $project->members()->pluck('user_id')->toArray();
        $workspaceMembers = $project->workspace->members()->pluck('id')->toArray();

        $availableMembers = array_diff($workspaceMembers, $projectMembers);

        return User::whereIn('id', $availableMembers)->get();
    }
}
