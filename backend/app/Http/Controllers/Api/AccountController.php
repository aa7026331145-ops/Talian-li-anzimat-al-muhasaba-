<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(): JsonResponse
    {
        $accounts = Account::with('children')
            ->whereNull('parent_id')
            ->get();

        return response()->json($accounts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'code'      => ['required', 'string', 'max:50'],
            'name'      => ['required', 'string', 'max:255'],
            'name_en'   => ['nullable', 'string', 'max:255'],
            'type'      => ['required', 'in:asset,liability,equity,revenue,expense'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $account = Account::create($data);

        return response()->json($account, 201);
    }

    public function show(Account $account): JsonResponse
    {
        return response()->json($account->load('children', 'parent'));
    }
}
