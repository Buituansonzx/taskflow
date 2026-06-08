<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Transformers;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class WorkspaceTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Workspace $workspace): array
    {
        return [
            'type' => $workspace->getResourceKey(),
            'id' => $workspace->id,
            'name' => $workspace->name,
            'slug' => $workspace->slug,
            'description' => $workspace->description,
            'owner_id' => $workspace->owner_id,
            'created_at' => $workspace->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $workspace->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
