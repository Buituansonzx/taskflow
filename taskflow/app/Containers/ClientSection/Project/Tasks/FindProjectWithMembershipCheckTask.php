<?php

namespace App\Containers\ClientSection\Project\Tasks;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FindProjectWithMembershipCheckTask extends ParentTask
{
    public function run(int $projectId, int $workspaceId, int $userId, array $relations = []): Project
    {
        $project = Project::with($relations)->where('id', $projectId)
            ->where('workspace_id', $workspaceId)
            ->firstOrFail();

        $isMember = $project->members()->where('user_id', $userId)->exists();
        if (!$isMember) {
            throw new HttpException(403, 'Bạn không có quyền thực hiện hành động này');
        }

        return $project;
    }
}
