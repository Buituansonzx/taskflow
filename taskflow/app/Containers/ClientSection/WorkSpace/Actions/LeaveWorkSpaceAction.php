<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class LeaveWorkSpaceAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->id;
        $user = $request->user();
        $workspace = Workspace::find($workspaceId);

        // 1. Check owner thì không cho rời
        if ($user->id === $workspace->owner_id) {
            throw new HttpException(400, 'Owner không thể rời workspace, hãy chuyển ownership trước');
        }

        // 2. Check user có phải trong workspace không
        $isMember = app(CheckWorkspaceMemberRoleTask::class)->run($user->id, $workspaceId, ['admin', 'member']);
        if (!$isMember) {
            throw new HttpException(400, 'Bạn không phải thành viên của workspace này');
        }

        // 3. Xóa role trong workspace
        setPermissionsTeamId($workspaceId);
        $user->syncRoles([]);


        return $workspace;
    }
}
