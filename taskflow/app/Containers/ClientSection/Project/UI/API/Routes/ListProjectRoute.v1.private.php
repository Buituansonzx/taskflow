<?php

/**
 * @apiGroup           Project
 * @apiName            
 *
 * @api                {GET} /v1/workspaces/:id/projects Listing
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

Route::get('workspaces/{id}/projects', [ProjectController::class, 'listing'])
    ->middleware(['auth:api']);

