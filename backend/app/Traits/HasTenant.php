<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->has('current_tenant')) {
                $builder->where(
                    static::qualifyColumn('tenant_id'),
                    app('current_tenant')->id
                );
            }
        });

        static::creating(function ($model) {
            if (app()->has('current_tenant') && empty($model->tenant_id)) {
                $model->tenant_id = app('current_tenant')->id;
            }
        });
    }

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
