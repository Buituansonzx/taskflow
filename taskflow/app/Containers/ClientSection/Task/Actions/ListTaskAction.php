<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Project\Tasks\FindProjectWithMembershipCheckTask;
use App\Containers\ClientSection\Task\Models\Task;
use App\Containers\ClientSection\Task\UI\API\Requests\ListTaskRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ListTaskAction extends ParentAction
{
    public function run(ListTaskRequest $request)
    {
        $data = $request->validated();
        $project = app(FindProjectWithMembershipCheckTask::class)->run(
            $request->project_id,
            $request->workspace_id,
            $request->user()->id,
            ['tags', 'members']
        );

        $query = Task::query()->where('project_id', $project->id)->whereNull('parent_task_id');

        if (!empty($data['assignee_id'])) {
            $query = $query->whereHas('assignees', function ($q) use ($data) {
                $q->where('user_id', $data['assignee_id']);
            });
        }

        if (!empty($data['status'])) {
            $query = $query->where('status', $data['status']);
        }

        if (!empty($data['priority'])) {
            $query = $query->where('priority', $data['priority']);
        }

        if (!empty($data['search'])) {
            $query = $query->where('title','like','%'.$data['search'].'%')
            ->orWhere('description','like','%'.$data['search'].'%');
        }

        if(!empty($data['tag_ids'])){
            $query = $query->whereHas('tags', function ($q) use ($data) {
                $q->whereIn('id', $data['tag_ids']);
            });
        }

        if(!empty($data['deadline_from'])){
            $query = $query->where('deadline','>=', $data['deadline_from']);
        }

        if(!empty($data['deadline_to'])){
            $query = $query->where('deadline','<=', $data['deadline_to']);
        }

        $sortBy    = !empty($data['sort_by']) ? $data['sort_by'] : 'created_at';
        $sortOrder = !empty($data['sort_order']) ? $data['sort_order'] : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->with(['assignees', 'tags', 'subTasks'])->get();
    }
}
