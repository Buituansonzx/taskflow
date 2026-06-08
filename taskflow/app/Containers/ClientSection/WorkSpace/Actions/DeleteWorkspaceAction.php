<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class DeleteWorkspaceAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspace = Workspace::find($request->id);
        $workspace->delete();
        return $workspace;
    }
}
