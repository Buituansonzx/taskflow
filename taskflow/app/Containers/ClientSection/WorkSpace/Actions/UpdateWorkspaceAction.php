<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\UpdateWorkspaceRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class UpdateWorkspaceAction extends ParentAction
{
    public function __construct(
        private readonly CheckWorkspaceMemberRoleTask $checkRoleTask
    )
    {
        
    }

    public function run(UpdateWorkspaceRequest $request)
    {
        
        $workspace = Workspace::findOrFail($request->id);
        $isOwner = $request->user()->id === $workspace->owner_id;
        $isAdmin = $this->checkRoleTask->run(
            $request->user()->id,
            $workspace->id,
            Role::ROLE_ADMIN,
        );
        if (!$isOwner && !$isAdmin) {
            throw new HttpException(403,'Bạn không có quyền sửa workspace này');
        }

        $data = array_filter([
            'description' => $request->description,
        ]);
        
        if (!empty($request->name)) {
            $slug = Str::slug($request->name);

            //Nếu trùng slug thì gắn thêm workspace_id vào slug mới
            $existingSlug = Workspace::where('slug', $slug)
                                    ->where('id', '!=', $workspace->id)
                                    ->exists();
            if ($existingSlug) {
                $slug = $slug . '-' . $workspace->id;
            }

            $data['name'] = $request->name;
            $data['slug'] = $slug;
        }

        $workspace->update($data);

        return $workspace;
    }
}
