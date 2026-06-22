<?php

namespace App\Containers\ClientSection\Task\Data\Repositories;

use App\Containers\ClientSection\Task\Models\Tag;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Tag
 *
 * @extends ParentRepository<TModel>
 */
final class TagRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
