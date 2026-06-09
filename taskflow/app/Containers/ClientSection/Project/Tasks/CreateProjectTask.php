<?php

namespace App\Containers\ClientSection\Project\Tasks;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\ClientSection\Project\Data\Repositories\ProjectRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Auth;
use Illuminate\Support\Facades\DB;

final class CreateProjectTask extends ParentTask
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function run(array $data)
    {
        try {
            $project = $this->projectRepository->create($data);
            $project->members()->attach(Auth::user()->id, [
                'role' => Role::ROLE_PROJECT_MANAGER,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $project;
    }
}
