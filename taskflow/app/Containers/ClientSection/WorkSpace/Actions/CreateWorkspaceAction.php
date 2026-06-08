<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\ClientSection\WorkSpace\Tasks\CreateWorkspaceTask;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\CreateWorkspaceRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\Auth;
use Log;
final class CreateWorkspaceAction extends ParentAction
{
    public function __construct(private readonly CreateWorkspaceTask $createWorkspaceTask)
    {
    }

    public function run(CreateWorkspaceRequest $request)
    {
        $user = Auth::user();
        $workspace = $this->createWorkspaceTask->run(
            $request->validated(),
            $user->id,
        );

        setPermissionsTeamId($workspace->id);
        $freshUser = User::findOrFail($user->id);
        $freshUser->assignRole('owner');

        return $workspace;
    }
}
