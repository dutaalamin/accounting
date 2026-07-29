<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $debit = $this->journalEntryLines()->sum('debit');
        $credit = $this->journalEntryLines()->sum('credit');

        if (in_array($this->type, ['asset', 'expense'])) {
            return $this->initial_balance + $debit - $credit;
        }

        return $this->initial_balance + $credit - $debit;
    }
}
