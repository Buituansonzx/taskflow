<?php

namespace App\Containers\ClientSection\WorkSpace\Tasks;

use App\Containers\ClientSection\WorkSpace\Data\Repositories\WorkspaceRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Str;

final class CreateWorkspaceTask extends ParentTask
{
    public function __construct(private readonly WorkspaceRepository $repository)
    {
    }

    public function run(array $data, $ownerId)
    {
        $dataWorkspace = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'owner_id' => $ownerId,
        ];

        $workspace = $this->repository->create($dataWorkspace);

        return $workspace;
    }
}
