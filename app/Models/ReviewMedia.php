<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewMedia extends Model
{
    protected $table = 'review_media';
    protected $fillable = ['id_review', 'media_path'];

    public function review()
    {
        return $this->belongsTo(Reviews::class, 'id_review', 'id_review');
    }
}
