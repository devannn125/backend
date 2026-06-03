<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hotels extends Model
{
    protected $table = 'hotels';
    protected $primaryKey = 'id_hotel';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_hotel', 'nama_hotel', 'alamat', 'kota', 'deskripsi', 'rating', 'email', 'no_hp', 'hotel_image'];
    protected $casts = ['rating' => 'decimal:2', 'created_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_hotel)) {
                $model->id_hotel = (string) Str::uuid();
            }
        });
    }

    public function facilities()
    {
        return $this->belongsToMany(Facilities::class, 'hotel_facilities', 'id_hotel', 'id_facility');
    }

    public function rooms()
    {
        return $this->hasMany(Rooms::class, 'id_hotel', 'id_hotel');
    }

    public function scopeByKota($query, string $kota)
    {
        return $query->where('kota', $kota);
    }

    public function scopeMinRating($query, float $rating)
    {
        return $query->where('rating', '>=', $rating);
    }
}
