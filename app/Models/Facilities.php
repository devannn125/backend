<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Facilities extends Model
{
    protected $table = 'facilities';
    protected $primaryKey = 'id_facility';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_facility', 'nama_facility'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_facility)) {
                $model->id_facility = (string) Str::uuid();
            }
        });
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotels::class, 'hotel_facilities', 'id_facility', 'id_hotel');
    }
}
