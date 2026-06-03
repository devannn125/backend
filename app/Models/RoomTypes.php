<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoomTypes extends Model
{
    protected $table = 'room_types';
    protected $primaryKey = 'id_room_type';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_room_type', 'nama_type', 'kapasitas', 'harga_per_malam', 'deskripsi'];
    protected $casts = ['kapasitas' => 'integer', 'harga_per_malam' => 'decimal:0'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->id_room_type)) {
                $model->id_room_type = 'RT-' . strtoupper(Str::random(10));
            }
        });
    }

    public function rooms()
    {
        return $this->hasMany(Rooms::class, 'id_room_type', 'id_room_type');
    }
}
