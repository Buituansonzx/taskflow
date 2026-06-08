<?php

namespace App\Containers\ClientSection\WorkSpace\Actions;

use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ListWorkspaceAction extends ParentAction
{
    public function run(Request $request)
    {
        
        $user = $request->user();

        //Làm chủ những workspace nào
        $ownerOf = Workspace::where('owner_id', $user->id)->pluck('id');

        //Làm member của những workspace nào
        $memberOf = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', get_class($user))
        ->pluck('team_id');
        

        //Merge 
        $workspaceIds = $ownerOf->merge($memberOf)->unique();

        return Workspace::whereIn('id', $workspaceIds)->get();
    }
}
