<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Project\UI\API\Requests\UpdateProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateProjectAction extends ParentAction
{
    public function run(UpdateProjectRequest $request)
    {
        $project = Project::with(['members', 'workspace'])
            ->where('id', $request->project_id)
            ->where('workspace_id', $request->workspace_id)
            ->firstOrFail();
        $data = array_filter([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if($project){
            $project->update($data);
        }

        return $project;
    }
}
