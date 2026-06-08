<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Models\WorkspaceInvitation;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AcceptInvitationAction extends ParentAction
{
    public function run($request)
    {
        $invitation = WorkspaceInvitation::where('token', $request->token)->first();

        if (!$invitation) {
            throw new HttpException(404, 'Lời mời không tồn tại');
        }

        if ($invitation->accepted_at) {
            throw new HttpException(400, 'Lời mời đã được chấp nhận');
        }

        if ($invitation->expired_at < now()) {
            throw new HttpException(400, 'Lời mời đã hết hạn');
        }

        $user = User::where('email', $invitation->email)->first();

        if (!$user) {
            throw new HttpException(404, 'Người dùng không tồn tại');
        }

        // Gắn role vào workspace
        setPermissionsTeamId($invitation->workspace_id);
        $freshUser = User::findOrFail($user->id);
        $freshUser->assignRole($invitation->role); 


        $invitation->update([
            'accepted_at' => now(),
        ]);

        return $user;
    }
}
