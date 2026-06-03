<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingAddons extends Model
{
    protected $table = 'bookings_addons';
    protected $primaryKey = 'id_booking_addon';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id_booking_addon', 'id_booking', 'id_addon', 'quantity', 'subtotal', 'catatan'];
    protected $casts = ['quantity' => 'integer', 'subtotal' => 'decimal:0'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_booking_addon)) {
                $model->id_booking_addon = (string) Str::uuid();
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'id_booking', 'id_booking');
    }

    public function addon()
    {
        return $this->belongsTo(Addons::class, 'id_addon', 'id_addon');
    }
}
