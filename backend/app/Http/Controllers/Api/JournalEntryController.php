<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function __construct(private JournalEntryService $service) {}

    public function index(): JsonResponse
    {
        $entries = JournalEntry::with('lines.account')
            ->latest('date')
            ->paginate(20);

        return response()->json($entries);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fiscal_year_id'        => ['required', 'exists:fiscal_years,id'],
            'date'                  => ['required', 'date'],
            'description'           => ['nullable', 'string'],
            'lines'                 => ['required', 'array', 'min:2'],
            'lines.*.account_id'    => ['required', 'exists:chart_of_accounts,id'],
            'lines.*.debit'         => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit'        => ['nullable', 'numeric', 'min:0'],
            'lines.*.description'   => ['nullable', 'string'],
        ]);

        $entry = $this->service->create($data);

        return response()->json($entry->load('lines.account'), 201);
    }

    public function show(JournalEntry $journalEntry): JsonResponse
    {
        return response()->json($journalEntry->load('lines.account', 'fiscalYear'));
    }

    public function post(JournalEntry $journalEntry): JsonResponse
    {
        $entry = $this->service->post($journalEntry);

        return response()->json($entry->load('lines.account'));
    }
}
