<?php

namespace App\Containers\ClientSection\Project\UI\API\Transformers;

use App\Containers\ClientSection\Project\Models\Project;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use Illuminate\Support\Facades\Auth;

final class ProjectTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Project $project): array
    {
        return [
            'type' => $project->getResourceKey(),
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'workspace_id' => $project->workspace_id,
            'role' => $project->members()->where('user_id', Auth::id())?->first()?->pivot?->role,
            'created_at' => $project->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $project->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
