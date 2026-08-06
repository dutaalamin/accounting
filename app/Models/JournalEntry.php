<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
        'date' => 'date',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->lines()->sum('debit');
    }

    /**
     * Posting jurnal: kunci agar tidak bisa diedit/dihapus.
     */
    public function post(): void
    {
        if ($this->is_posted) {
            return;
        }
        $this->update([
            'is_posted' => true,
            'posted_at' => now(),
        ]);
    }

    /**
     * Cek apakah jurnal sudah diposting (terkunci).
     */
    public function isPosted(): bool
    {
        return (bool) $this->is_posted;
    }
}
