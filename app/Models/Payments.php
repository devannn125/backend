<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    public $incrementing = false;
    protected $keyType = 'string';
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    // Metode pembayaran
    const METODE_TRANSFER        = 'transfer';
    const METODE_EWALLET         = 'ewallet';
    const METODE_CREDIT_CARD     = 'credit_card';
    const METODE_VIRTUAL_ACCOUNT = 'virtual_account';
    const METODE_QRIS            = 'qris';

    // Status pembayaran
    const STATUS_SUCCESS = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCEL  = 'cancel';

    protected $fillable = [
        'id_payment', 'id_booking', 'metode_pembayaran',
        'jumlah_bayar', 'status_pembayaran', 'expired_at', 'paid_at',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'created_at'   => 'datetime',
        'expired_at'   => 'datetime',
        'paid_at'      => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_payment)) {
                $model->id_payment = self::generateId();
            }
        });
    }

    private static function generateId(): string
    {
        $last = self::where('id_payment', 'like', 'PAY%')
                    ->orderByRaw('CAST(SUBSTRING(id_payment, 4) AS UNSIGNED) DESC')
                    ->value('id_payment');

        if (!$last) {
            return 'PAY001';
        }

        $number = (int) substr($last, 3); // ambil angka setelah "PAY"
        return 'PAY' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'id_booking', 'id_booking');
    }

    // Helper methods
    public function isSuccess(): bool
    {
        return $this->status_pembayaran === self::STATUS_SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status_pembayaran === self::STATUS_PENDING;
    }

    public function isCancelled(): bool
    {
        return $this->status_pembayaran === self::STATUS_CANCEL;
    }
}