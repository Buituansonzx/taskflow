<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class DeleteWorkspaceAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspace = Workspace::find($request->id);
        DB::transaction(function () use ($workspace) {
            $workspace->projects()->delete();
            $workspace->delete();
        });
        return $workspace;
    }
}
