<?php

namespace App\Containers\ClientSection\Project\Tasks;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CheckProjectMemberRoleTask extends ParentTask
{
    public function run(int $userId, int $projectId, string|array $roles): bool
    {
        return Project::find($projectId)
            ?->members()
            ->wherePivotIn('role', (array) $roles)
            ->wherePivot('user_id', $userId)
            ->exists();
    }
}
