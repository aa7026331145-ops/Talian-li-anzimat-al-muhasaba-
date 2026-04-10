<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'description',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit'  => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journalEntry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
