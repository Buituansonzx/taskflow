<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Project\Tasks\FindProjectWithMembershipCheckTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;

final class ListTagAction extends ParentAction
{
    public function run(Request $request)
    {
        $project = app(FindProjectWithMembershipCheckTask::class)->run(
            $request->project_id,
            $request->workspace_id,
            $request->user()->id,
            ['tags', 'members']
        );

        return $project->tags;
    }
}
