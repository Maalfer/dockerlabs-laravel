<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaquinaBunker extends Model
{
    protected $table = 'maquinas_bunkerlabs';

    protected $fillable = [
        'nombre',
        'descripcion',
        'dificultad',
        'enlace_descarga',
        'autor',
        'autor_email',
        'user_id',
        'imagen_path',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen_path ? asset('storage/'.$this->imagen_path) : null;
    }

    protected $appends = ['dificultad_clase', 'dificultad_etiqueta'];

    public function getDificultadClaseAttribute(): string
    {
        $nivel = strtolower(str_replace(' ', '-', (string)($this->dificultad ?? '')));
        return match ($nivel) {
            'muy-facil' => 'badge-cyan',
            'facil'     => 'badge-green',
            'medio'     => 'badge-amber',
            'dificil'   => 'badge-red',
            default     => 'badge-gray',
        };
    }

    public function getDificultadEtiquetaAttribute(): string
    {
        $nivel = strtolower(str_replace(' ', '-', (string)($this->dificultad ?? '')));
        return match ($nivel) {
            'muy-facil' => 'Muy Fácil',
            'facil'     => 'Fácil',
            'medio'     => 'Medio',
            'dificil'   => 'Difícil',
            default     => ucfirst($nivel ?: 'N/D'),
        };
    }

    public function scopeDifficulty($query, ?string $nivel)
    {
        $niveles = ['muy-facil', 'facil', 'medio', 'dificil'];
        if (!in_array($nivel, $niveles, true)) {
            return $query;
        }
        return $query->whereRaw('LOWER(REPLACE(dificultad, " ", "-")) = ?', [$nivel]);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
