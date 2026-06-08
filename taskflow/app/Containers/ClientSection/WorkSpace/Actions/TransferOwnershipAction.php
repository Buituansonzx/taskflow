<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\TransferOwnershipRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class TransferOwnershipAction extends ParentAction
{
    public function run(TransferOwnershipRequest $request)
    {
        $newEmail = $request->email;
        $newUser = User::where('email', $newEmail)->first();
        if (!$newUser) {
            throw new HttpException(404, 'Không tìm thấy user với email ' . $newEmail);
        }
        $workspaceId = $request->id;
        $workspace = Workspace::find($workspaceId);
        $currentUser = $request->user();
        $newOwner = $newUser;
        if ($currentUser->id === $newOwner->id) {
            throw new HttpException(400, 'Bạn không thể chuyển quyền sở hữu cho chính mình');
        }
        $isMember = app(CheckWorkspaceMemberRoleTask::class)->run($newUser->id, $workspaceId, ['admin', 'member']);
        if (!$isMember) {
            throw new HttpException(400, 'Bạn không phải thành viên của workspace này');
        }
        DB::transaction(function () use ($workspace, $currentUser, $newOwner, $workspaceId) {
            // 4. Đổi owner_id trong workspace
            $workspace->update(['owner_id' => $newOwner->id]);

            // 5. Gán role owner cho user mới
            setPermissionsTeamId($workspaceId);
            $freshNewOwner = User::findOrFail($newOwner->id);
            $freshNewOwner->syncRoles(['owner']);

            // 6. Đổi role owner cũ thành admin
            $freshCurrentUser = User::findOrFail($currentUser->id);
            $freshCurrentUser->syncRoles(['admin']);
        });
    }
}
