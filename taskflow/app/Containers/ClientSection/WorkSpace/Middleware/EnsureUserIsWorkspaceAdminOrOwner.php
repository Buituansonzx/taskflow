<?php

namespace App\Containers\ClientSection\WorkSpace\Middleware;

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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $workspaceId = $request->route('workspace_id') ?? $request->route('id');
        $workspace = Workspace::findOrFail($workspaceId);

        $isOwner = $request->user()->id === $workspace->owner_id;

        $isMember = app(CheckWorkspaceMemberRoleTask::class)->run(
            $request->user()->id,
            $workspace->id,
            ['admin','member']
        );

        if (!$isOwner && !$isMember) {
            throw new HttpException(403, 'Bạn không thuộc workspace này');
        }

        // Nếu chỉ cho phép owner
        if (in_array('owner', $roles) && count($roles) === 1) {
            if (!$isOwner) {
                throw new HttpException(403, 'Chỉ owner mới có quyền thực hiện hành động này');
            }
            return $next($request);
        }

        $hasRole = $this->checkRoleTask->run(
            $request->user()->id,
            $workspace->id,
            $roles
        );

        if (!$isOwner && !$hasRole) {
            throw new HttpException(403, 'Bạn không có quyền thực hiện hành động này');
        }

        return $next($request);
    }
}
