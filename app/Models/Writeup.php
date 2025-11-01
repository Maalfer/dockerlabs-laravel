<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Writeup extends Model
{
    // Si tu tabla se llama distinto de "writeups", descomenta la siguiente línea:
    // protected $table = 'nombre_de_tu_tabla';

    protected $fillable = [
        'maquina_id',
        'autor',
        'enlace',
    ];

    /**
     * Relación con la máquina a la que pertenece el writeup
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class);
    }

    /**
     * Relación con el usuario si el autor está registrado
     * (usa el campo "autor" de la tabla writeups y lo compara con "name" de users)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor', 'name');
    }
}
