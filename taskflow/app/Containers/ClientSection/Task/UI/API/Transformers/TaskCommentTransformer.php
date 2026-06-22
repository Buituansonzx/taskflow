<?php

namespace App\Containers\ClientSection\Task\UI\API\Transformers;

use App\Containers\ClientSection\Task\Models\Comment;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TaskCommentTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Comment $comment): array
    {
        return [
            'type'       => $comment->getResourceKey(),
            'id'         => $comment->id,
            'user'       => [
                'id'   => $comment->user->id,
                'name' => $comment->user->name,
            ],
            'content'    => $comment->content,
            'replies'    => $comment->replies->map(fn($reply) => [
                'id'         => $reply->id,
                'user'       => [
                    'id'   => $reply->user->id,
                    'name' => $reply->user->name,
                ],
                'content'    => $reply->content,
                'created_at' => $reply->created_at->format('Y-m-d H:i:s'),
            ]),
            'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
