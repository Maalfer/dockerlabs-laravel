<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Writeup extends Model
{
    protected $fillable = ['maquina_id', 'autor', 'enlace'];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Maquina::class);
    }
}
