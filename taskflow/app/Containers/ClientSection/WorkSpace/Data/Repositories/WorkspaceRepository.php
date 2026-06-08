<?php

namespace App\Containers\ClientSection\WorkSpace\Data\Repositories;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Workspace
 *
 * @extends ParentRepository<TModel>
 */
final class WorkspaceRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
