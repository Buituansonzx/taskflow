<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\ClientSection\Project\UI\API\Requests\GetTaskActivitiesRequest;
use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Ship\Parents\Actions\Action as ParentAction;

final class GetTaskActivitiesAction extends ParentAction
{
    public function run(GetTaskActivitiesRequest $request){
        $taskActivities = TaskActivity::with('actor')
            ->where('project_id', $request->project_id);
        if($request->action){
            $taskActivities->where('action', $request->action);
        }
        if($request->from_date){
            $taskActivities->where('created_at', '>=', $request->from_date);
        }
        if($request->to_date){
            $taskActivities->where('created_at', '<=', $request->to_date);
        }
        if($request->actor_id){
            $taskActivities->where('actor_id', $request->actor_id);
        }
        return $taskActivities->latest()->paginate(10);
    }
}
