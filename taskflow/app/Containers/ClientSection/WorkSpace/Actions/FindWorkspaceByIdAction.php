<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;

final class FindWorkspaceByIdAction extends ParentAction
{
    public function run(Request $request)
    {
        $workspaceId = $request->id;

        $workspace = Workspace::with('members', 'owner')->findOrFail($workspaceId);
        
        return $workspace;
    }
}
