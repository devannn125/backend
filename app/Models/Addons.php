<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Addons extends Model
{
    protected $table = 'addons';
    protected $primaryKey = 'id_addon';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    const STATUS_AVAILABLE = 'available';
    const STATUS_UNAVAILABLE = 'unavailable';

    protected $fillable = ['id_addon', 'nama_addon', 'deskripsi', 'harga', 'status'];

    protected $casts = ['harga' => 'decimal:0'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_addon)) {
                $model->id_addon = (string) Str::uuid();
            }
        });
    }

    public function bookingAddons()
    {
        return $this->hasMany(BookingAddons::class, 'id_addon', 'id_addon');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama_addon', 'like', "%{$keyword}%")
                ->orWhere('deskripsi', 'like', "%{$keyword}%");
        });
    }
}
