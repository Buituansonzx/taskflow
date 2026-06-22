<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Task\Models\Tag;
use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Containers\ClientSection\Task\Tasks\FindTaskWithProjectCheckTask;
use App\Containers\ClientSection\Task\UI\API\Requests\RemoveTagForTaskRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RemoveTagForTaskAction extends ParentAction
{
    public function run(RemoveTagForTaskRequest $request)
    {
        $task = app(FindTaskWithProjectCheckTask::class)->run(
            $request->task_id,
            $request->project_id,
            $request->workspace_id,
            $request->user()->id,
            ['assignees', 'tags', 'project', 'project.tags']
        );
        $project = $task->project;
        $tags = $request->tags;
        $projectTagIds = $project->tags->pluck('id')->toArray();

        foreach($tags as $tag){
            if(!in_array($tag, $projectTagIds)){
                throw new HttpException(422, "Tag {$tag} không tồn tại trong project");
            }
            if(!$task->tags->contains($tag)){
                throw new HttpException(422, "Tag {$tag} không tồn tại trong task");
            }
        }
        $task->tags()->detach($tags);
        TaskActivity::create([
            'task_id'    => $task->id,
            'project_id' => $task->project_id,
            'actor_id'   => auth()->id(),
            'action'     => TaskActivity::ACTION_TAG_REMOVED,
            'old_value'  => ['tag_ids' => $tags, 'tags' => Tag::whereIn('id', $tags)->pluck('name', 'id')], 
            'new_value'  => null,
        ]);
        return $task;
    }
}
