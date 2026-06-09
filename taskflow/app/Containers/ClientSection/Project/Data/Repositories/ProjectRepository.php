<?php

namespace App\Containers\ClientSection\Project\Data\Repositories;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Project
 *
 * @extends ParentRepository<TModel>
 */
final class ProjectRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
