<?php

/**
 * @apiGroup           Project
 * @apiName            
 *
 * @api                {GET} /v1/workspaces/:workspace_id/projects/:project_id/available-members Get Available Members
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

use App\Containers\ClientSection\Project\UI\API\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('workspaces/{workspace_id}/projects/{project_id}/available-members', [ProjectController::class, 'getAvailableMembers'])
    ->middleware(['auth:api','project.manage']);

