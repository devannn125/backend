<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table        = 'user';
    protected $primaryKey   = 'id_user';
    public    $incrementing = false;
    protected $keyType      = 'string';

    public $timestamps   = false;
    const CREATED_AT     = 'created_at';

    protected $fillable = [
        'id_user',
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat',
        'user_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->id_user)) {
                $lastUser = self::orderBy('id_user', 'desc')->first();

                if ($lastUser && preg_match('/^USR(\d+)$/', $lastUser->id_user, $matches)) {
                    $nextNumber = (int) $matches[1] + 1;
                } else {
                    $nextNumber = 1;
                }

                $model->id_user = 'USR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}