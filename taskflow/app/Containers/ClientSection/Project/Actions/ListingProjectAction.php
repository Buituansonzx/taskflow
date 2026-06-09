<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ListingProjectAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->id;

        $userId = Auth::id();

        $isOwnerOrAdmin = app(CheckWorkspaceMemberRoleTask::class)->run($workspaceId, $userId, [Role::ROLE_OWNER, Role::ROLE_ADMIN]);

        if ($isOwnerOrAdmin) {
            // Xem tất cả project trong workspace
            $projects = Project::where('workspace_id', $workspaceId)->get();
        } else {
            // Chỉ xem project mình là member
            $projects = Project::where('workspace_id', $workspaceId)
                ->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->get();
        }

        return $projects->load('members');
    }
}
