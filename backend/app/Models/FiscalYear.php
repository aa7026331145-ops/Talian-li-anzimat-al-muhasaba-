<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'starts_at',
        'ends_at',
        'is_closed',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'is_closed' => 'boolean',
    ];

    public function journalEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}
