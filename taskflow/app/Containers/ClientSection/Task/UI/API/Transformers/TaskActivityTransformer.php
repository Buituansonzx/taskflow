<?php

namespace App\Containers\ClientSection\Task\UI\API\Transformers;

use App\Containers\ClientSection\Task\Models\TaskActivity;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TaskActivityTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(TaskActivity $taskactivity): array
    {
        return [
            'type' => $taskactivity->getResourceKey(),
            'id' => $taskactivity->id,
            'actor' => [
                'id' => $taskactivity->actor->id,
                'name' => $taskactivity->actor->name,
            ],
            'action' => $taskactivity->action,
            'old_value' => $taskactivity->old_value,
            'new_value' => $taskactivity->new_value,
            'created_at' => $taskactivity->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
