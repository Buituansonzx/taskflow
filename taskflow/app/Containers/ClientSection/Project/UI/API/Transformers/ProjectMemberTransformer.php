<?php

namespace App\Containers\ClientSection\Project\UI\API\Transformers;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class ProjectMemberTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(User $user): array
    {
        return [
            'type' => $user->getResourceKey(),
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->pivot->role, //Không cần $user->projects() vì $user đã được load từ $project->members()
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
