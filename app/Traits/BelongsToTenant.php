<?php

declare(strict_types=1);

namespace App\Traits;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            $tenantContext = app(TenantContext::class);

            if (!$tenantContext->isResolved()) {
                throw new RuntimeException('Tenant is not resolved for tenant-bound model creation.');
            }

            $model->club_id = $tenantContext->id();
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenantContext = app(TenantContext::class);

            if (!$tenantContext->isResolved()) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $builder->where($builder->getModel()->getTable() . '.club_id', $tenantContext->id());
        });
    }
}
