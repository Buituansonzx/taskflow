<?php

namespace App\Containers\ClientSection\Project\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateProjectRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'name' => 'string',
            'description' => 'string|nullable',
        ];
    }
}
