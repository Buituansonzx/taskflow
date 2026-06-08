<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class CreateWorkspaceRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
        ];
    }
}
