<?php

namespace App\Containers\AppSection\Authentication\UI\API\Controllers;

use App\Containers\AppSection\Authentication\Actions\LoginAction;
use App\Containers\AppSection\Authentication\UI\API\Requests\LoginRequest;
use App\Containers\AppSection\Authentication\Values\UserCredential;
use App\Ship\Parents\Controllers\ApiController;

final class LoginController extends ApiController
{
    public function login(LoginRequest $request, LoginAction $action)
    {
        $data = $action->run($request,UserCredential::create(
            $request->validated('email'),
            $request->validated('password'),
        ));

        return response()->json($data, 200);
    }
}
