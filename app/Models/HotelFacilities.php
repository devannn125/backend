<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelFacilities extends Model
{
    protected $table = 'hotel_facilities';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_hotel', 'id_facility'];

    public function hotel()
    {
        return $this->belongsTo(Hotels::class, 'id_hotel', 'id_hotel');
    }

    public function facility()
    {
        return $this->belongsTo(Facilities::class, 'id_facility', 'id_facility');
    }
}
