<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Tag;
use App\Containers\ClientSection\Task\UI\API\Requests\UpdateTagRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class UpdateTagAction extends ParentAction
{
    public function run(UpdateTagRequest $request)
    {
        $project = Project::with('members')->where('id', $request->project_id)->where('workspace_id', $request->workspace_id)->firstOrFail();

        $existsMember = $project->members()->where('user_id', $request->user()->id)->exists();
        if(!$existsMember){
            throw new HttpException(403, 'Bạn không phải là thành viên của dự án này');
        }
        $tag = Tag::where('id', $request->tag_id)->where('project_id', $request->project_id)->firstOrFail();

        if($tag->project_id !== $project->id){
            throw new HttpException(403, 'Bạn không có quyền thực hiện hành động này');
        }
        $data = array_filter($request->validated());
        $tag->update($data);
        return $tag->fresh();
    }
}
