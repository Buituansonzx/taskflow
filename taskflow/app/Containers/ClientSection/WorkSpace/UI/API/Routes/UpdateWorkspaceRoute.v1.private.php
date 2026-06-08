<?php

/**
 * @apiGroup           WorkSpace
 * @apiName            
 *
 * @api                {PUT} /v1/workspaces/:id Update
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

Route::put('workspaces/{id}', [WorkspaceController::class, 'update'])
    ->middleware(['auth:api', 'workspace.role:owner,admin']);

