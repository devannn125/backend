<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bookings extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    const STATUS_SUCCESS = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCEL = 'cancel';

    protected $fillable = ['id_booking', 'id_user', 'tanggal_booking', 'check_in', 'check_out', 'total_harga', 'status'];
    protected $casts = ['tanggal_booking' => 'datetime', 'check_in' => 'date', 'check_out' => 'date', 'total_harga' => 'decimal:0'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_booking)) {
                $model->id_booking = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function bookingAddons()
    {
        return $this->hasMany(BookingAddons::class, 'id_booking', 'id_booking');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, string $idUser)
    {
        return $query->where('id_user', $idUser);
    }

    public function scopeDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('tanggal_booking', [$from, $to]);
    }
}
