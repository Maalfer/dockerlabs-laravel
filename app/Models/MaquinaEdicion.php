<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinaEdicion extends Model
{
    protected $table = 'maquinas_ediciones';

    protected $fillable = [
        'maquina_id',
        'user_id',
        'estado',
        'cambios',
        'comentario',
    ];

    protected $casts = [
        'cambios' => 'array',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Maquina::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
