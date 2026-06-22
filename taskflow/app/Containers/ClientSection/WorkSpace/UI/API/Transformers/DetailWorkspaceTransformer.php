<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Transformers;

use App\Containers\ClientSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class DetailWorkspaceTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [
        'members',
        'projects'
    ];

    protected array $availableIncludes = [];

    public function transform(Workspace $workspace): array
    {
        return [
            'type' => $workspace->getResourceKey(),
            'id' => $workspace->id,
            'name' => $workspace->name,
            'slug' => $workspace->slug,
            'description' => $workspace->description,
            'owner' => [
                'owner_id' => $workspace->owner_id,
                'name' => $workspace->owner->name,
                'email' => $workspace->owner->email,
            ],
            'created_at' => $workspace->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $workspace->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function includeMembers(Workspace $workspace){
        $members = $workspace->members;
        return $this->collection($members, new MemberWorkspaceTransformer());
    }

    public function includeProjects(Workspace $workspace){
        $projects = $workspace->projects;
        return $this->collection($projects, new ProjectTransformer());
    }

}
