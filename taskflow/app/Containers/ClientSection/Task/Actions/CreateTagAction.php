<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Data\Repositories\TagRepository;
use App\Containers\ClientSection\Task\UI\API\Requests\CreateTagRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class CreateTagAction extends ParentAction
{
    public function __construct(public readonly TagRepository $repository){}
    
    public function run(CreateTagRequest $request)
    {
        $project = Project::where('id', $request->project_id)->where('workspace_id', $request->workspace_id)->first();
        if(!$project){
            throw new HttpException(404, 'Không tìm thấy dự án');
        }
         $tag = $this->repository->create([
            'name' => $request->name,
            'color' => $request->color,
            'project_id' => $request->project_id,
         ]);
         return $tag;
    }
}
