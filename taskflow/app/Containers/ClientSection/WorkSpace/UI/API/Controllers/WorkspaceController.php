<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\WorkSpace\Actions\AcceptInvitationAction;
use App\Containers\ClientSection\WorkSpace\Actions\CreateWorkspaceAction;
use App\Containers\ClientSection\WorkSpace\Actions\DeleteWorkspaceAction;
use App\Containers\ClientSection\WorkSpace\Actions\FindWorkspaceByIdAction;
use App\Containers\ClientSection\WorkSpace\Actions\InviteToWorkspaceAction;
use App\Containers\ClientSection\WorkSpace\Actions\LeaveWorkSpaceAction;
use App\Containers\ClientSection\WorkSpace\Actions\ListWorkspaceAction;
use App\Containers\ClientSection\WorkSpace\Actions\RemoveMemberAction;
use App\Containers\ClientSection\WorkSpace\Actions\TransferOwnershipAction;
use App\Containers\ClientSection\WorkSpace\Actions\UpdateWorkspaceAction;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\CreateWorkspaceRequest;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\InviteToWorkspaceRequest;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\TransferOwnershipRequest;
use App\Containers\ClientSection\WorkSpace\UI\API\Requests\UpdateWorkspaceRequest;
use App\Containers\ClientSection\WorkSpace\UI\API\Transformers\DetailWorkspaceTransformer;
use App\Containers\ClientSection\WorkSpace\UI\API\Transformers\WorkspaceTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\Request;

final class WorkSpaceController extends ApiController
{
    public function create(CreateWorkspaceRequest $request, CreateWorkspaceAction $action)
    {
        $workspace = $action->run($request);

        return Response::create($workspace, WorkspaceTransformer::class);
    }

    public function list(Request $request, ListWorkspaceAction $action)
    {
        $workspaces = $action->run($request);

        return Response::create($workspaces, WorkspaceTransformer::class);
    }

    public function invite(InviteToWorkspaceRequest $request, InviteToWorkspaceAction $action)
    {
        $action->run($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Gửi lời mời thành công',
        ], 200);
    }

    public function accept(Request $request, AcceptInvitationAction $action){
        $action->run($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Chấp nhận lời mời thành công',
        ], 200);
    }

    public function findWorkspaceById(Request $request,FindWorkspaceByIdAction $action){
        $workspace = $action->run($request);
        return Response::create($workspace, DetailWorkspaceTransformer::class);
    }

    public function update(UpdateWorkspaceRequest $request, UpdateWorkspaceAction $action){
        $workspace = $action->run($request);
        return Response::create($workspace, WorkspaceTransformer::class);
    }

    public function delete(Request $request, DeleteWorkspaceAction $action){
        $action->run($request);
        return response()->json([
            'status' => 'success',
            'message' => 'Xóa workspace thành công',
        ], 200);
    }

    public function removeMember(Request $request, RemoveMemberAction $action){
        $data = $action->run($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa thành viên ' . $data['member_email'] . ' khỏi workspace   ' . $data['workspace_name'] . ' thành công',
        ], 200);
    }

    public function leave(Request $request, LeaveWorkSpaceAction $action){
        $workspace = $action->run($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Rời khỏi workspace ' . $workspace->name . ' thành công',
        ], 200);
    }

    public function transferOwnership(TransferOwnershipRequest $request, TransferOwnershipAction $action){
        $workspace = $action->run($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Chuyển ownership workspace ' . $workspace->name . ' thành công',
        ], 200);
    }
}
