<?php

namespace App\Containers\ClientSection\Project\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\Project\Actions\CreateProjectAction;
use App\Containers\ClientSection\Project\Actions\FindProjectByIdAction;
use App\Containers\ClientSection\Project\Actions\ListingProjectAction;
use App\Containers\ClientSection\Project\Actions\UpdateProjectAction;
use App\Containers\ClientSection\Project\UI\API\Requests\CreateProjectRequest;
use App\Containers\ClientSection\Project\UI\API\Requests\UpdateProjectRequest;
use App\Containers\ClientSection\Project\UI\API\Transformers\DetailProjectTransformer;
use App\Containers\ClientSection\Project\UI\API\Transformers\ProjectTransformer;
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
}
