<?php

namespace App\Containers\AppSection\Authentication\Actions;

use App\Containers\AppSection\Authentication\Data\Factories\PasswordTokenFactory;
use App\Containers\AppSection\Authentication\UI\API\Requests\LoginRequest;
use App\Containers\AppSection\Authentication\Values\Clients\WebClient;
use App\Containers\AppSection\Authentication\Values\RequestProxies\PasswordGrant\AccessTokenProxy;
use App\Containers\AppSection\Authentication\Values\UserCredential;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Hash;

final class LoginAction extends ParentAction
{
    public function __construct(
        private readonly PasswordTokenFactory $factory,
    ) {}

    public function run(LoginRequest $request,UserCredential $credential)
    {
        $data = $request->validated();

        $email = $data['email'];
        $password = $data['password'];

        $user = User::where('email', $email)->first();
        $roles = $user->roles()->get();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if(!$user->email_verified_at){
            return response()->json([
                'message' => 'Email not verified',
            ], 401);
        }

        $token =  $this->factory->make(
            AccessTokenProxy::create(
                $credential,
                WebClient::create(),
            ),
        );

        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'token' => $token,
        ], 200);
    }
}
