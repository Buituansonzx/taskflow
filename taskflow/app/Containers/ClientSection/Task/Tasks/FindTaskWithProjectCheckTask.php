<?php

namespace App\Containers\ClientSection\Task\Tasks;

use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FindTaskWithProjectCheckTask extends ParentTask
{
    public function run(int $taskId, int $projectId, int $workspaceId, int $userId, array $relations = []): Task
    {
        $project = Project::with('members')
            ->where('id', $projectId)
            ->where('workspace_id', $workspaceId)
            ->firstOrFail();

        $isMember = $project->members()->where('user_id', $userId)->exists();
        if (!$isMember) {
            throw new HttpException(403, 'Bạn không có quyền thực hiện hành động này');
        }

        return Task::with($relations)
            ->where('id', $taskId)
            ->where('project_id', $project->id)
            ->firstOrFail();
    }
}
