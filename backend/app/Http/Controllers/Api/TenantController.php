<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Tenant::paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:100', 'unique:tenants,slug'],
            'email'     => ['nullable', 'email'],
            'phone'     => ['nullable', 'string'],
            'address'   => ['nullable', 'string'],
            'currency'  => ['nullable', 'string', 'size:3'],
            'locale'    => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $tenant = Tenant::create($data);

        return response()->json($tenant, 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        return response()->json($tenant);
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'slug'      => ['sometimes', 'string', 'max:100', 'unique:tenants,slug,'.$tenant->id],
            'email'     => ['nullable', 'email'],
            'phone'     => ['nullable', 'string'],
            'address'   => ['nullable', 'string'],
            'currency'  => ['nullable', 'string', 'size:3'],
            'locale'    => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $tenant->update($data);

        return response()->json($tenant);
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return response()->json(null, 204);
    }
}
