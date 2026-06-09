<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\ClientSection\Project\Tasks\CreateProjectTask;
use App\Containers\ClientSection\Project\UI\API\Requests\CreateProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class CreateProjectAction extends ParentAction
{
    public function run(CreateProjectRequest $request)
    {
        $data = $request->validated();

        $data['workspace_id'] = $request->id;

        $project = app(CreateProjectTask::class)->run($data);
        return $project;
    }
}
