<?php

namespace App\Containers\ClientSection\Project\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\Project\Actions\AddMemberToProjectAction;
use App\Containers\ClientSection\Project\Actions\CreateProjectAction;
use App\Containers\ClientSection\Project\Actions\DeleteProjectAction;
use App\Containers\ClientSection\Project\Actions\FindProjectByIdAction;
use App\Containers\ClientSection\Project\Actions\GetAvailableMembersAction;
use App\Containers\ClientSection\Project\Actions\GetTaskActivitiesAction;
use App\Containers\ClientSection\Project\Actions\ListingProjectAction;
use App\Containers\ClientSection\Project\Actions\RemoveMemberProjectAction;
use App\Containers\ClientSection\Project\Actions\UpdateProjectAction;
use App\Containers\ClientSection\Project\UI\API\Requests\AddMemberToProjectRequest;
use App\Containers\ClientSection\Project\UI\API\Requests\CreateProjectRequest;
use App\Containers\ClientSection\Project\UI\API\Requests\GetTaskActivitiesRequest;
use App\Containers\ClientSection\Project\UI\API\Requests\RemoveMemberProjectRequest;
use App\Containers\ClientSection\Project\UI\API\Requests\UpdateProjectRequest;
use App\Containers\ClientSection\Project\UI\API\Transformers\DetailProjectTransformer;
use App\Containers\ClientSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\ClientSection\Task\UI\API\Transformers\TaskActivityTransformer;
use App\Containers\ClientSection\WorkSpace\Actions\RemoveMemberAction;
use App\Containers\ClientSection\Workspace\UI\API\Transformers\MemberWorkspaceTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\Request;

final class ProjectController extends ApiController
{
    public function create(CreateProjectRequest $request, CreateProjectAction $action){
        $project = $action->run($request);
        return Response::create($project, ProjectTransformer::class);
    }

    public function listing(Request $request, ListingProjectAction $action){
        $projects = $action->run($request);
        return Response::create($projects, ProjectTransformer::class);
    }

    public function findProjectById(Request $request, FindProjectByIdAction $action){
        $project = $action->run($request);
        return Response::create($project, DetailProjectTransformer::class);
    }

    public function update(UpdateProjectRequest $request, UpdateProjectAction $action){
        $project = $action->run($request);
        return Response::create($project, ProjectTransformer::class);
    }

    public function delete(Request $request,DeleteProjectAction $action){
        $action->run($request);
        return response()->json([
            'success' => true,
            'message' => 'Xóa dự án thành công.',
        ],200);
    }

    public function addMembers(AddMemberToProjectRequest $request, AddMemberToProjectAction $action){
        $project = $action->run($request);
        return Response::create($project, DetailProjectTransformer::class);
    }

    public function getAvailableMembers(Request $request, GetAvailableMembersAction $action){
        $members = $action->run($request);
        return Response::create($members, MemberWorkspaceTransformer::class);
    }

    public function removeMembers(RemoveMemberProjectRequest $request, RemoveMemberProjectAction $action){
        $action->run($request);
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thành viên khỏi dự án.',
        ],200);
    }

    public function getTaskActivities(GetTaskActivitiesRequest $request, GetTaskActivitiesAction $action){
        $activities = $action->run($request);
        return Response::create($activities, TaskActivityTransformer::class);
    }
}
