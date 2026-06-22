<?php

namespace App\Containers\ClientSection\Project\Actions;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\Project\Models\Project;
use App\Containers\ClientSection\Project\UI\API\Requests\AddMemberToProjectRequest;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AddMemberToProjectAction extends ParentAction
{
    public function run(AddMemberToProjectRequest $request)
    {
        $projectId = $request->project_id;
        $workspaceId = $request->workspace_id;
        
        $project = Project::with(['workspace', 'members'])->where('id', $projectId)->where('workspace_id', $workspaceId)->firstOrFail();

        $members = $request->members;

        foreach ($members as $member) {
            $email = $member['email'];
            $role  = $member['role'] ?? 'developer';

            $user = User::where('email', $email)->first();
            if (!$user) {
                throw new HttpException(404, "Email {$email} không tồn tại trong hệ thống");
            }
            $isWorkspaceMember = app(CheckWorkspaceMemberRoleTask::class)->run($user->id, $workspaceId, [Role::ROLE_MEMBER, Role::ROLE_OWNER, Role::ROLE_ADMIN]);
            $isOwner = $project->workspace->owner_id == $user->id;

            if (!$isWorkspaceMember && !$isOwner) {
                throw new HttpException(400, "User {$email} không thuộc workspace này");
            }

            $alreadyInProject = $project->members()
                ->wherePivot('user_id', $user->id)
                ->exists();
            if ($alreadyInProject) {
                throw new HttpException(400, "User {$email} đã là thành viên của dự án");
            }

            $project->members()->attach($user->id, ['role' => $role]);
            
        }
        return $project->load('members');
    }
}
