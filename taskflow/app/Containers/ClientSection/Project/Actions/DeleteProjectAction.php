<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;

final class DeleteProjectAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->workspace_id;
        $projectId = $request->project_id;

        $project = Project::with(['members', 'workspace'])
            ->where('id', $projectId)
            ->where('workspace_id', $workspaceId)
            ->firstOrFail();
        
        $project->delete();
    }
}
