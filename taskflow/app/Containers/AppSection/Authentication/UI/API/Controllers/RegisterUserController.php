<?php

namespace App\Containers\AppSection\Authentication\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\AppSection\Authentication\Actions\RegisterUserAction;
use App\Containers\AppSection\Authentication\UI\API\Requests\RegisterUserRequest;
use App\Containers\AppSection\User\UI\API\Transformers\UserTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class RegisterUserController extends ApiController
{
    public function __invoke(RegisterUserRequest $request, RegisterUserAction $action)
    {
        $data = $action->transactionalRun($request->sanitize([
            'email',
            'password',
            'name',
            'gender',
            'birth',
        ]));

        return response()->json([
            'user' => $data['user'],
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'],
        ], 201);
    }
}
