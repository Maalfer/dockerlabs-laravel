<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioMaquina extends Model
{
    protected $table = 'envios_maquinas';

    protected $fillable = [
        'nombre_maquina',
        'dificultad',
        'autor_nombre',
        'autor_enlace',
        'fecha_creacion',
        'writeup',
        'enlace_descarga',
        'imagen_path',
        'destino',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen_path ? asset('storage/'.$this->imagen_path) : null;
    }
}
