<?php

namespace App\Containers\ClientSection\WorkSpace\Tasks;

use App\Containers\ClientSection\WorkSpace\Data\Repositories\WorkspaceInvitationRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Str;

final class CreateWorkspaceInvitationTask extends ParentTask
{
    public function __construct(private readonly WorkspaceInvitationRepository $repository)
    {
    }

    public function run(array $data)
    {
        $data['workspace_id'] = $data['workspace_id'] ?? null;
        $data['invited_by']   = $data['invited_by']   ?? null;
        $data['token']        = Str::uuid();
        $data['expired_at']   = now()->addDays(7);

        return $this->repository->create($data);
    }
}
