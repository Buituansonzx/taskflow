<?php

namespace App\Containers\AppSection\Authentication\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tasks\CreateUserTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

final class RegisterUserAction extends ParentAction
{
    public function __construct(
        private readonly CreateUserTask $createUserTask,
    ) {
    }

    public function run(array $data): array
    {
        $result = DB::transaction(function () use ($data) {
            $user = $this->createUserTask->run($data);

            event(new Registered($user));

            $token = $user->createToken('register-token');

            return [
                'user'         => $user,
                'access_token' => $token->accessToken,
                'token_type'   => 'Bearer',
            ];
        });

        return $result;
    }
}
