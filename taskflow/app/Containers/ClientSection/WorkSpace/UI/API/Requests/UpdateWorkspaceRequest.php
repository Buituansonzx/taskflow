<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateWorkspaceRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
