<?php

namespace App\Containers\ClientSection\Workspace\UI\API\Transformers;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class MemberWorkspaceTransformer extends ParentTransformer
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
        ];
    }
}
