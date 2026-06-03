<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reviews extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id_review';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = ['id_review', 'id_user', 'id_hotel', 'rating', 'komentar'];
    protected $casts = ['rating' => 'integer', 'created_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->id_review)) {
                $model->id_review = 'RV-' . strtoupper(Str::random(10));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotels::class, 'id_hotel', 'id_hotel');
    }

    public function scopeMinRating($query, int $min)
    {
        return $query->where('rating', '>=', $min);
    }
}
