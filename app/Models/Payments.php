<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payments extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    public $incrementing = false;
    protected $keyType = 'string';
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    const METODE_EWALLET = 'ewallet';
    const METODE_CREDIT_CARD = 'credit_card';
    const METODE_VIRTUAL_ACCOUNT = 'virtual_account';
    const STATUS_SUCCESS = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCEL = 'cancel';

    protected $fillable = ['id_payment', 'id_booking', 'metode_pembayaran', 'jumlah_bayar', 'status_pembayaran', 'expired_at', 'paid_at'];
    protected $casts = ['jumlah_bayar' => 'decimal:2', 'created_at' => 'datetime', 'expired_at' => 'datetime', 'paid_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_payment)) {
                $model->id_payment = (string) Str::uuid();
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'id_booking', 'id_booking');
    }

    public function isSuccess(): bool
    {
        return $this->status_pembayaran === self::STATUS_SUCCESS;
    }

    public function isCancelled(): bool
    {
        return $this->status_pembayaran === self::STATUS_CANCEL;
    }
}
