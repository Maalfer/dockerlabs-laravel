<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Maquina extends Model
{
       protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'dificultad',
        'enlace_descarga',
        'autor',
        'autor_email',
        'imagen_path',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen_path ? asset('storage/' . $this->imagen_path) : null;
    }

    public function getDificultadClaseAttribute(): string
    {
        $slug = Str::slug((string) $this->dificultad);
        return match ($slug) {
            'facil', 'muy-facil' => 'badge--easy',
            'medio'              => 'badge--medium',
            'dificil'            => 'badge--hard',
            default              => 'badge--unknown',
        };
    }

    public function getDificultadEtiquetaAttribute(): string
    {
        $raw = (string) ($this->dificultad ?? '');
        $nice = str_replace('-', ' ', $raw);
        return $nice !== '' ? ucfirst($nice) : 'Sin dificultad';
    }

    public function writeupsTemporales()
    {
        return $this->hasMany(\App\Models\WriteupTemporal::class, 'maquina_id');
    }

    public function writeups()
    {
        return $this->hasMany(\App\Models\Writeup::class, 'maquina_id');
    }

    public function scopeDifficulty($query, ?string $nivel)
    {
        $niveles = ['muy-facil', 'facil', 'medio', 'dificil'];
        if (!in_array($nivel, $niveles)) {
            return $query;
        }
        return $query->whereRaw('LOWER(REPLACE(dificultad, " ", "-")) = ?', [$nivel]);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
