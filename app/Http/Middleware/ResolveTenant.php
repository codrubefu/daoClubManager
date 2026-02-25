<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Club;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $tenantSlug = (string) $request->header('X-Tenant', '');

        if ($tenantSlug === '') {
            return new JsonResponse([
                'message' => 'Tenant header is required.',
                'errors' => [
                    'X-Tenant' => ['Missing tenant header.'],
                ],
            ], 422);
        }

        $club = Club::query()->where('slug', $tenantSlug)->first();

        if (!$club) {
            return new JsonResponse([
                'message' => 'Tenant not found.',
            ], 404);
        }

        app(TenantContext::class)->setTenant((int) $club->id, (string) $club->slug);

        return $next($request);
    }
}
