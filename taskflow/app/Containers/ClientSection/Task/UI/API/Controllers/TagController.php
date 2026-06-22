<?php

namespace App\Containers\ClientSection\Task\UI\API\Controllers;

use App\Containers\ClientSection\Task\Actions\CreateTagAction;
use App\Containers\ClientSection\Task\Actions\DeleteTagAction;
use App\Containers\ClientSection\Task\Actions\ListTagAction;
use App\Containers\ClientSection\Task\Actions\UpdateTagAction;
use App\Containers\ClientSection\Task\UI\API\Requests\CreateTagRequest;
use App\Containers\ClientSection\Task\UI\API\Requests\UpdateTagRequest;
use App\Containers\ClientSection\Task\UI\API\Transformers\TagTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Apiato\Http\Response;
use Illuminate\Http\Request;

final class TagController extends ApiController
{
    public function create(CreateTagRequest $request, CreateTagAction $action){
        $tag = $action->run($request);
        return Response::create($tag, TagTransformer::class);
    }

    public function list(Request $request, ListTagAction $action){
        $tags = $action->run($request);
        return Response::create($tags, TagTransformer::class);
    }

    public function update(UpdateTagRequest $request, UpdateTagAction $action){
        $tag = $action->run($request);
        return Response::create($tag, TagTransformer::class);
    }

    public function delete(Request $request, DeleteTagAction $action){
        $action->run($request);
        return response()->json([
            'status' => 204,
            'message' => "Xóa tag thành công",
        ]);
    }
}
