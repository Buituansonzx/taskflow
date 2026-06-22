<?php

/**
 * @apiGroup           Task
 * @apiName            
 *
 * @api                {POST} /v1/workspaces/:workspace_id/projects/:project_id/tags Create
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

use App\Containers\ClientSection\Task\UI\API\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::post('workspaces/{workspace_id}/projects/{project_id}/tags', [TagController::class, 'create'])
    ->middleware(['auth:api','project.manage']);

