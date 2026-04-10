<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $tenantId = $request->header('X-Tenant-ID')
            ?? $request->query('tenant_id')
            ?? ($request->user()?->tenant_id);

        if ($tenantId) {
            $tenant = Tenant::where('id', $tenantId)
                ->orWhere('slug', $tenantId)
                ->where('is_active', true)
                ->first();

            if ($tenant) {
                app()->instance('current_tenant', $tenant);
            }
        }

        return $next($request);
    }
}
