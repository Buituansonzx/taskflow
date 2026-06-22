<?php

namespace App\Containers\ClientSection\Task\Data\Repositories;

use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of TaskActivity
 *
 * @extends ParentRepository<TModel>
 */
final class TaskActivityRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
