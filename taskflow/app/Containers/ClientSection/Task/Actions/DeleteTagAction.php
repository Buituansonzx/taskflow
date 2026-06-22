<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Tag;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class DeleteTagAction extends ParentAction
{
    public function run(Request $request)
    {
        $tag = Tag::where('id', $request->tag_id)->where('project_id', $request->project_id)->firstOrFail();
        $tag->delete();
    }
}
