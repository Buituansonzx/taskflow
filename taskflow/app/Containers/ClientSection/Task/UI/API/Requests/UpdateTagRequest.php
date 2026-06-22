<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateTagRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'color' => 'string|max:255',
        ];
    }
}
