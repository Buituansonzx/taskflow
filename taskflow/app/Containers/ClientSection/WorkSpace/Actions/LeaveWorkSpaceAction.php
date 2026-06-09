<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class LeaveWorkSpaceAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->id;
        $user = $request->user();
        $workspace = Workspace::find($workspaceId);

        // 1. Nếu là owner
        if ($user->id === $workspace->owner_id) {
            // Check còn member nào khác không
            $hasOtherMembers = DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('team_id', $workspaceId)
                ->where('model_id', '!=', $user->id)
                ->exists();

            if ($hasOtherMembers) {
                // Còn member khác → không cho rời
                throw new HttpException(400, 'Hãy chuyển ownership trước khi rời workspace');
            }

            // Không còn ai khác → xóa workspace luôn
            $workspace->delete();
            return $workspace;
        }

        // 2. Check user có phải trong workspace không
        $isMember = app(CheckWorkspaceMemberRoleTask::class)->run($user->id, $workspaceId, ['admin', 'member']);
        if (!$isMember) {
            throw new HttpException(400, 'Bạn không phải thành viên của workspace này');
        }

        // 3. Xóa role trong workspace
        setPermissionsTeamId($workspaceId);
        $user->syncRoles([]);

        // 4. Check còn member nào không
        $remainingMembers = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('team_id', $workspaceId)
            ->count();

        if ($remainingMembers === 0) {
            $workspace->projects()->delete();
            $workspace->delete(); // tự động xóa workspace
        }
        return $workspace;
    }
}
