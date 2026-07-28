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
}
