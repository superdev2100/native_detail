<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\LineStatus;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'date',
        'payment_date',
        'status',
        'line_status',
        'amount',
        'type', // 'income' or 'expense'
        'category_id',
        'description',
        'payment_method',
        'reference_number',
        'user_id', // who recorded the transaction
        'member_id', // The user who is making the payment
    ];

    protected $casts = [
        'date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'boolean',
        'line_status' => LineStatus::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); // The user who recorded the transaction
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id'); // The user who is making the payment
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(FinanceTag::class, 'finance_transaction_finance_tag');
    }
}
