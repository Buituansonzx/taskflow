<?php

/**
 * @apiGroup           WorkSpace
 * @apiName            
 *
 * @api                {GET} /v1/workspaces List
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

use App\Containers\ClientSection\WorkSpace\UI\API\Controllers\Controller;
use App\Containers\ClientSection\WorkSpace\UI\API\Controllers\WorkSpaceController;
use Illuminate\Support\Facades\Route;

Route::get('workspaces', [WorkSpaceController::class, 'list'])
    ->middleware(['auth:api']);

