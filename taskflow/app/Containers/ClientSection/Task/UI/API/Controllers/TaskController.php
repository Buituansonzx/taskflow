<?php

namespace App\Containers\ClientSection\Task\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\Task\Actions\DeleteTaskAction;
use App\Containers\ClientSection\Task\Actions\FindTaskByIdAction;
use App\Containers\ClientSection\Task\Actions\GetActivitiesTaskAction;
use App\Containers\ClientSection\Task\Actions\ListTaskAction;
use App\Containers\ClientSection\Task\Actions\CreateTaskAction;
use App\Containers\ClientSection\Task\Actions\RemoveTagForTaskAction;
use App\Containers\ClientSection\Task\Actions\UnassignMemberAction;
use App\Containers\ClientSection\Task\Actions\UpdateTaskAction;
use App\Containers\ClientSection\Task\UI\API\Requests\CreateTaskRequest;
use App\Containers\ClientSection\Task\UI\API\Requests\ListTaskRequest;
use App\Containers\ClientSection\Task\UI\API\Requests\RemoveTagForTaskRequest;
use App\Containers\ClientSection\Task\UI\API\Requests\UnassignMemberRequest;
use App\Containers\ClientSection\Task\UI\API\Requests\UpdateTaskRequest;
use App\Containers\ClientSection\Task\UI\API\Transformers\DetailTaskTransformer;
use App\Containers\ClientSection\Task\UI\API\Transformers\TaskActivityTransformer;
use App\Containers\ClientSection\Task\UI\API\Transformers\TaskTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\Request;

final class TaskController extends ApiController
{
    public function create(CreateTaskRequest $request, CreateTaskAction $action){
        $task = $action->run($request);
        return Response::create($task, TaskTransformer::class);
    }

    public function list(ListTaskRequest $request, ListTaskAction $action){
        $tasks = $action->run($request);
        return Response::create($tasks, TaskTransformer::class);
    }

    public function detail(Request $request, FindTaskByIdAction $action){
        $task = $action->run($request);
        return Response::create($task, DetailTaskTransformer::class);
    }

    public function update(UpdateTaskRequest $request, UpdateTaskAction $action){
        $task = $action->run($request);
        return Response::create($task, DetailTaskTransformer::class);
    }

    public function unassignMember(UnassignMemberRequest $request, UnassignMemberAction $action){
        $task = $action->run($request);
        return Response::create($task, DetailTaskTransformer::class);
    }

    public function removeTagForTask(RemoveTagForTaskRequest $request, RemoveTagForTaskAction $action){
        $task = $action->run($request);
        return Response::create($task, DetailTaskTransformer::class);
    }

    public function delete(Request $request, DeleteTaskAction $action){
        $action->run($request);
        return response()->noContent();
    }

    public function getActivities(Request $request, GetActivitiesTaskAction $action){
        $activities = $action->run($request);
        return Response::create($activities, TaskActivityTransformer::class);
    }
}
