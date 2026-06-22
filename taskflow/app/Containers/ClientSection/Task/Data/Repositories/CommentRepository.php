<?php

namespace App\Containers\ClientSection\Task\Data\Repositories;

use App\Containers\ClientSection\Task\Models\Comment;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Comment
 *
 * @extends ParentRepository<TModel>
 */
final class CommentRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
