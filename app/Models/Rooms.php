<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Rooms extends Model
{
    protected $table = 'rooms';
    protected $primaryKey = 'id_room';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const STATUS_AVAILABLE = 'available';
    const STATUS_BOOKED = 'booked';
    const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = ['id_room', 'id_hotel', 'id_room_type', 'room_image', 'nomor_kamar', 'status'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->id_room)) {
                $model->id_room = 'RM-' . strtoupper(Str::random(10));
            }
        });
    }

    public function hotel()
    {
        return $this->belongsTo(Hotels::class, 'id_hotel', 'id_hotel');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomTypes::class, 'id_room_type', 'id_room_type');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
}
