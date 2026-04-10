<?php

namespace App\Services;

use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalEntryService
{
    /**
     * Create a new journal entry with lines.
     *
     * @param  array{fiscal_year_id: int, date: string, description: ?string, lines: array}  $data
     */
    public function create(array $data): JournalEntry
    {
        $this->validateBalance($data['lines']);

        return DB::transaction(function () use ($data) {
            $tenant = app('current_tenant');

            $number = $this->nextNumber($tenant->id, $data['fiscal_year_id']);

            $entry = JournalEntry::create([
                'tenant_id'      => $tenant->id,
                'fiscal_year_id' => $data['fiscal_year_id'],
                'number'         => $number,
                'date'           => $data['date'],
                'description'    => $data['description'] ?? null,
                'status'         => 'draft',
                'created_by'     => auth()->id(),
            ]);

            foreach ($data['lines'] as $line) {
                $entry->lines()->create([
                    'account_id'  => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit'       => $line['debit'] ?? 0,
                    'credit'      => $line['credit'] ?? 0,
                ]);
            }

            return $entry->load('lines');
        });
    }

    /**
     * Post a draft journal entry.
     */
    public function post(JournalEntry $entry): JournalEntry
    {
        if ($entry->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => ['القيد مُرحَّل مسبقاً.'],
            ]);
        }

        $entry->load('lines');
        $this->validateBalance($entry->lines->map(fn ($l) => [
            'debit'  => $l->debit,
            'credit' => $l->credit,
        ])->all());

        $entry->update([
            'status'    => 'posted',
            'posted_at' => now(),
        ]);

        return $entry;
    }

    private function validateBalance(array $lines): void
    {
        $totalDebit  = collect($lines)->sum(fn ($l) => (float) ($l['debit'] ?? 0));
        $totalCredit = collect($lines)->sum(fn ($l) => (float) ($l['credit'] ?? 0));

        if (abs($totalDebit - $totalCredit) >= 0.001) {
            throw ValidationException::withMessages([
                'lines' => [
                    sprintf(
                        'القيد غير متوازن: مجموع المدين (%.2f) لا يساوي مجموع الدائن (%.2f).',
                        $totalDebit,
                        $totalCredit
                    ),
                ],
            ]);
        }

        if ($totalDebit <= 0) {
            throw ValidationException::withMessages([
                'lines' => ['يجب أن يحتوي القيد على سطر واحد على الأقل بمبلغ موجب.'],
            ]);
        }
    }

    private function nextNumber(int $tenantId, int $fiscalYearId): string
    {
        $max = JournalEntry::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('number');

        $seq = $max ? ((int) substr($max, strrpos($max, '-') + 1)) + 1 : 1;

        return sprintf('JE-%s-%04d', $fiscalYearId, $seq);
    }
}
