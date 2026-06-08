<?php

namespace App\Containers\ClientSection\WorkSpace\Middleware;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\ClientSection\WorkSpace\Models\Workspace;
use App\Containers\ClientSection\WorkSpace\Tasks\CheckWorkspaceMemberRoleTask;
use App\Ship\Parents\Middleware\Middleware as ParentMiddleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class EnsureUserIsWorkspaceAdminOrOwner extends ParentMiddleware
{
    public function __construct(
        private readonly CheckWorkspaceMemberRoleTask $checkRoleTask
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $workspaceId = $request->route('workspace_id') ?? $request->route('id');
        $workspace = Workspace::findOrFail($workspaceId);

        $isOwner = $request->user()->id === $workspace->owner_id;
        $isAdmin = $this->checkRoleTask->run(
            $request->user()->id,
            $workspace->id,
            Role::ROLE_ADMIN
        );

        if (!$isOwner && !$isAdmin) {
            throw new HttpException(403, 'Bạn không có quyền thực hiện hành động này');
        }

        return $next($request);
    }
}
