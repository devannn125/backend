<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
                $model->id_review = self::generateId();
            }
        });
    }

    private static function generateId(): string
    {
        $last = self::where('id_review', 'like', 'RVW%')
                    ->orderByRaw('CAST(SUBSTRING(id_review, 4) AS UNSIGNED) DESC')
                    ->value('id_review');

        if (!$last) {
            return 'RVW001';
        }

        $number = (int) substr($last, 3); // ambil angka setelah "RVW"
        return 'RVW' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotels::class, 'id_hotel', 'id_hotel');
    }

    // Scopes
    public function scopeMinRating($query, int $min)
    {
        return $query->where('rating', '>=', $min);
    }
}