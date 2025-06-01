<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FinanceTag extends Model
{
    protected $fillable = [
        'name',
        'color',
        'description',
    ];

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(FinanceTransaction::class, 'finance_transaction_finance_tag');
    }
}
