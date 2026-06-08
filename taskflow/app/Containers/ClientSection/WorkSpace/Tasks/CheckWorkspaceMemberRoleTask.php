<?php

namespace App\Containers\ClientSection\WorkSpace\Tasks;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\DB;

final class CheckWorkspaceMemberRoleTask extends ParentTask
{
    public function run(int $userId, int $workspaceId, string|array $roles): bool
    {
        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $userId)
            ->where('model_has_roles.model_type', User::class)
            ->where('model_has_roles.team_id', $workspaceId)
            ->whereIn('roles.name', (array) $roles)
            ->exists();
    }
}
