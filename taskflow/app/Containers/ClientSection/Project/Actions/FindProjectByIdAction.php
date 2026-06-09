<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Actions\Action as ParentAction;

final class FindProjectByIdAction extends ParentAction
{
    public function run($request)
    {

        $project = Project::with(['members', 'workspace'])
            ->where('id', $request->project_id)
            ->where('workspace_id', $request->workspace_id)
            ->firstOrFail();
        return $project;
    }
}
