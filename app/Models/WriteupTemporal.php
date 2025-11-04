<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WriteupTemporal extends Model
{
    protected $table = 'writeups_temporal';

    protected $fillable = [
       'maquina_id',
       'autor',
       'autor_email',  
       'enlace',
       'estado',
       'user_id',      
       'tipo',
       'comentario',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Maquina::class);
    }

    public function writeup(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Writeup::class);
    }
}
