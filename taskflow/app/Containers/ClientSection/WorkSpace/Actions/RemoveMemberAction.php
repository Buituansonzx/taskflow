<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Events\InviteToWorkspaceEvent;
use App\Containers\ClientSection\Workspace\Events\RemoveMemberEvent;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;

final class RemoveMemberAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->workspace_id;
        $userId = $request->member_id;

        $workspace = Workspace::find($workspaceId);
        $user = User::find($userId);

        setPermissionsTeamId($workspaceId);
        $user->roles()->detach(); // xóa tất cả role trong workspace đó

        RemoveMemberEvent::dispatch($workspace, $user->email);
        // Hoặc xóa role cụ thể
        // $user->removeRole('member');

        return [
            'member_email' => $user->email,
            'workspace_name' => $workspace->name,
        ];
    }
}
