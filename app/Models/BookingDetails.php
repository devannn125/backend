<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingDetails extends Model
{
    protected $table = 'booking_details';
    protected $primaryKey = 'id_booking_detail';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const STATUS_SUCCESS = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCEL = 'cancel';

    protected $fillable = ['id_booking_detail', 'id_booking', 'id_room', 'harga', 'jumlah_malam', 'subtotal', 'status'];
    protected $casts = ['harga' => 'decimal:2', 'subtotal' => 'decimal:2', 'jumlah_malam' => 'integer'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_booking_detail)) {
                $model->id_booking_detail = (string) Str::uuid();
            }

            if (empty($model->subtotal) && $model->harga && $model->jumlah_malam) {
                $model->subtotal = $model->harga * $model->jumlah_malam;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['harga', 'jumlah_malam'])) {
                $model->subtotal = $model->harga * $model->jumlah_malam;
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'id_booking', 'id_booking');
    }

    public function room()
    {
        return $this->belongsTo(Rooms::class, 'id_room', 'id_room');
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCEL;
    }
}
