<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    const STATUS_SUCCESS = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCEL  = 'cancel';

    protected $fillable = [
        'id_user', 'tanggal_booking', 'check_in',
        'check_out', 'total_harga', 'status',
    ];

    protected $casts = [
        'tanggal_booking' => 'datetime',
        'check_in'        => 'date',
        'check_out'       => 'date',
        'total_harga'     => 'decimal:0',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->id_booking = self::generateId();
        });
    }

    private static function generateId(): string
    {
        $last = self::where('id_booking', 'like', 'BK%')
                    ->orderByRaw('CAST(SUBSTRING(id_booking, 3) AS UNSIGNED) DESC')
                    ->value('id_booking');

        if (!$last) {
            return 'BK001';
        }

        $number = (int) substr($last, 2);
        return 'BK' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetails::class, 'id_booking', 'id_booking');
    }

    public function bookingAddons()
    {
        return $this->hasMany(BookingAddons::class, 'id_booking', 'id_booking');
    }

    public function payments()
    {
        return $this->hasOne(Payments::class, 'id_booking', 'id_booking');
    }

    // Scopes
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