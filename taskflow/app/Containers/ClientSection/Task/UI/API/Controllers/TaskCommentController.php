<?php

namespace App\Containers\ClientSection\Task\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\Task\Actions\ListTaskCommentAction;
use App\Containers\ClientSection\Task\UI\API\Requests\CreateTaskCommentRequest;
use App\Containers\ClientSection\Task\UI\API\Transformers\TaskCommentTransformer;
use App\Ship\Parents\Controllers\ApiController;
use App\Containers\ClientSection\Task\Actions\CreateTaskCommentAction;
use App\Containers\ClientSection\Task\Actions\DeleteTaskCommentAction;
use App\Containers\ClientSection\Task\Actions\UpdateTaskCommentAction;
use Illuminate\Http\Request;

final class TaskCommentController extends ApiController
{
    public function list(Request $request, ListTaskCommentAction $action)
    {
        $data = $action->run($request);
        return Response::create($data, TaskCommentTransformer::class);
    }

    public function create(CreateTaskCommentRequest $request, CreateTaskCommentAction $action)
    {
        $data = $action->run($request);
        return Response::create($data, TaskCommentTransformer::class);
    }
}
