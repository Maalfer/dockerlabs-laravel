<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BunkerToken extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'token_hash',
        'token_ciphertext',
        'expires_at',
        'used_at',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
        'active'     => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
