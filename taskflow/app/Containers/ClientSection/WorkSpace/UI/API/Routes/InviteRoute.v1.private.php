<?php

/**
 * @apiGroup           WorkSpace
 * @apiName            
 *
 * @api                {POST} /v1/workspaces/:id/invitations Invite
 * @apiDescription     Endpoint description here...
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} parameters here...
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     // Insert the response of the request here...
 * }
 */

use App\Containers\ClientSection\WorkSpace\UI\API\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::post('workspaces/{id}/invitations', [WorkspaceController::class, 'invite'])
    ->middleware(['auth:api', 'workspace.role:admin,owner']);

