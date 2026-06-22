<?php

namespace App\Containers\ClientSection\Task\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Tag;
use App\Containers\ClientSection\Task\Models\Task;
use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Containers\ClientSection\Task\UI\API\Requests\CreateTaskRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class CreateTaskAction extends ParentAction
{
    public function run(CreateTaskRequest $request){
        $workspaceId = $request->workspace_id;
        $projectId = $request->project_id;
        $project = Project::with('members')->where('id',$projectId)->where('workspace_id',$workspaceId)->first();
        if(!$project){
            throw new HttpException(404, 'Không tìm thấy dự án');
        }
        $data = $request->validated();
        $dataTask = [
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'],
            'status' => $data['status'],
            'created_by' => $request->user()->id,
            'deadline' => $data['deadline'] ?? null,
            'parent_task_id' => $data['parent_task_id'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
        ];
        //Kiểm tra xem task cha có cùng project hay không
        if (isset($data['parent_task_id'])) {
            $parentTask = Task::where('id', $data['parent_task_id'])
                ->where('project_id', $projectId)
                ->first();

            if (!$parentTask) {
                throw new HttpException(400, 'Task cha không thuộc project này');
            }
        }
        //Tạo task
        $task = Task::create($dataTask);
        //Thêm người vào task
        if(isset($data['assignees'])){
            //Kiểm tra xem người đó có trong project hay không
            $validMemberIds = $project->members()
                ->whereIn('user_id', $data['assignees'])
                ->pluck('user_id')
                ->toArray();
            $invalidMembers = array_diff($data['assignees'], $validMemberIds);
            if (!empty($invalidMembers)) {
                throw new HttpException(400, 'Một số người dùng không phải thành viên của project');
            }
            $task->assignees()->attach($validMemberIds);

            // Log assignee
            TaskActivity::create([
                'task_id'    => $task->id,
                'project_id' => $task->project_id,
                'actor_id'   => auth()->id(),
                'action'     => TaskActivity::ACTION_ASSIGNEE_ADDED,
                'old_value'  => null,
                'new_value' => [
                    'user_ids' => $validMemberIds,
                    'users'    => User::whereIn('id', $validMemberIds)->pluck('name', 'id'),
                ]
            ]);
        }
        //Thêm tag vào task
        if(isset($data['tags'])){
            //Kiểm tra xem tag có trong project hay không
            $validTagIds = Tag::where('project_id', $projectId)
                ->whereIn('id', $data['tags'])
                ->pluck('id')
                ->toArray();

            $invalidTags = array_diff($data['tags'], $validTagIds);
            if (!empty($invalidTags)) {
                throw new HttpException(400, 'Một số tag không thuộc project này');
            }

            $task->tags()->sync($validTagIds);
            TaskActivity::create([
                'task_id'    => $task->id,
                'project_id' => $task->project_id,
                'actor_id'   => auth()->id(),
                'action'     => TaskActivity::ACTION_TAG_ADDED,
                'old_value'  => null,
                'new_value'  => [
                    'tag_ids' => $validTagIds,
                    'tags'    => Tag::whereIn('id', $validTagIds)->pluck('name', 'id'),
                ],
            ]);
        }
        
        return $task;
    }
}
