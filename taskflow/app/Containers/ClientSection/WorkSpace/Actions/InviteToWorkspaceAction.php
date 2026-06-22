<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Events\InviteToWorkspaceEvent;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CreateWorkspaceInvitationTask;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\InviteToWorkspaceRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InviteToWorkspaceAction extends ParentAction
{

    public function __construct(private readonly CreateWorkspaceInvitationTask $task){

    }
    public function run(InviteToWorkspaceRequest $request)
    {
        
        $workspaceId = $request->id;

        foreach ($request->members as $member) {
            $email = $member['email'];
            $role = $member['role'];
            $data = [
                'email' => $email,
                'role' => $role,
                'workspace_id' => $workspaceId,
                'invited_by' => $request->user()->id,
            ];
            $existEmail = User::where('email', $email)->first();
            if (!$existEmail) {
                throw new HttpException(404,'Email này chưa có tài khoản trong hệ thống');
            }
            $alreadyMember = DB::table('model_has_roles')
                ->where('model_id', $existEmail->id)
                ->where('model_type', User::class)
                ->where('team_id', $workspaceId)
                ->exists();
            if ($alreadyMember) {
                throw new HttpException(409,'User này đã là thành viên của workspace');
            }
            $invitation = $this->task->run($data);
            InviteToWorkspaceEvent::dispatch($invitation);
        }
    }
}
