<?php

namespace App\Containers\ClientSection\Project\Middleware;

use App\Containers\ClientSection\Project\Tasks\CheckProjectMemberRoleTask;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Ship\Parents\Middleware\Middleware as ParentMiddleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class EnsureUserIsProjectRoles extends ParentMiddleware
{

    public function __construct(
        private readonly CheckWorkspaceMemberRoleTask $checkWorkspaceRole,
        private readonly CheckProjectMemberRoleTask $checkProjectRole,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $workspaceId = $request->route('workspace_id');
        $projectId   = $request->route('project_id');
        $userId      = $request->user()->id;
        $workspace   = Workspace::findOrFail($workspaceId);

        $isOwner = $userId === $workspace->owner_id;

        $isWorkspaceAdmin = $this->checkWorkspaceRole->run($userId, $workspaceId, 'admin');

        $isProjectManager = $this->checkProjectRole->run($userId, $projectId, 'project-manager');

        if (!$isOwner && !$isWorkspaceAdmin && !$isProjectManager) {
            throw new HttpException(403, 'Bạn không có quyền thực hiện hành động này');
        }

        return $next($request);
    }
}
