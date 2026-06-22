<?php

namespace App\Containers\ClientSection\Task\UI\API\Transformers;

use App\Containers\ClientSection\Task\Models\Tag;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TagTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Tag $tag): array
    {
        return [
            'type' => $tag->getResourceKey(),
            'id' => $tag->id,
            'name' => $tag->name,
            'color' => $tag->color,
            'created_at' => $tag->created_at->format('H:i d/m/Y'),
            'updated_at' => $tag->updated_at->format('H:i d/m/Y'),
        ];
    }
}
