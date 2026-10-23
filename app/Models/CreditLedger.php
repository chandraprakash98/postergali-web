<?php

namespace App\Models;

use Database\Factories\CreditLedgerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class CreditLedger extends Model
{
    /** @use HasFactory<CreditLedgerFactory> */
    use HasFactory;

    protected $table = 'credit_ledger';

    public $timestamps = false;

    public const TYPE_CREDIT = 'CREDIT';
    public const TYPE_DEBIT = 'DEBIT';
    public const TYPE_REVERSAL = 'REVERSAL';

    public const CATEGORY_WALLET_TOPUP = 'WALLET_TOPUP';
    public const CATEGORY_POSTER_PURCHASE = 'POSTER_PURCHASE';
    public const CATEGORY_REFUND = 'REFUND';
    public const CATEGORY_BONUS = 'BONUS';
    public const CATEGORY_ADMIN_ADJUSTMENT = 'ADMIN_ADJUSTMENT';

    protected $fillable = [
        'ledger_uuid',
        'customer_id',
        'payment_transaction_id',
        'entry_type',
        'transaction_category',
        'credit_amount',
        'debit_amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'idempotency_key',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'credit_amount' => 'decimal:2',
        'debit_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CreditLedger $entry): void {
            if (empty($entry->ledger_uuid)) {
                $entry->ledger_uuid = (string) Str::uuid();
            }

            if (empty($entry->created_at)) {
                $entry->created_at = now();
            }

            $credit = (float) ($entry->credit_amount ?? 0);
            $debit = (float) ($entry->debit_amount ?? 0);

            if ($credit < 0 || $debit < 0) {
                throw new InvalidArgumentException('Credit and debit amounts in ledger entry cannot be negative.');
            }

            if ($credit > 0 && $debit > 0) {
                throw new InvalidArgumentException('A ledger entry cannot have both positive credit and positive debit amounts.');
            }
        });

        static::updating(function (): void {
            throw new RuntimeException('CreditLedger is strictly append-only. Modifying existing entries is prohibited.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('CreditLedger is strictly append-only. Deleting existing entries is prohibited.');
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function scopeByCustomer(Builder $query, string $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }
}
