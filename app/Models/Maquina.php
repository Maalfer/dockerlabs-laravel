<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Maquina extends Model
{
    // Se agrega el campo 'enlace_descarga' a los campos 'fillable' para permitir la asignación masiva
    protected $fillable = ['nombre', 'descripcion', 'dificultad', 'enlace_descarga'];

    /**
     * Devuelve la clase CSS según la dificultad.
     */
    public function getDificultadClaseAttribute(): string
    {
        $slug = Str::slug((string) $this->dificultad); // ej: "facil", "medio", "dificil", "muy-facil"

        return match ($slug) {
            'facil', 'muy-facil' => 'badge--easy',
            'medio'              => 'badge--medium',
            'dificil'            => 'badge--hard',
            default              => 'badge--unknown',
        };
    }

    /**
     * Devuelve la etiqueta de la dificultad en formato bonito.
     */
    public function getDificultadEtiquetaAttribute(): string
    {
        $raw = (string) ($this->dificultad ?? '');
        $nice = str_replace('-', ' ', $raw);
        return $nice !== '' ? ucfirst($nice) : 'Sin dificultad';
    }

    /**
     * Writeups temporales enviados (pendientes de aprobación).
     */
    public function writeupsTemporales()
    {
        return $this->hasMany(\App\Models\WriteupTemporal::class, 'maquina_id');
    }

    /**
     * Writeups aprobados y publicados.
     */
    public function writeups()
    {
        return $this->hasMany(\App\Models\Writeup::class, 'maquina_id');
    }

    /**
     * Scope para filtrar por dificultad (muy-facil, facil, medio, dificil).
     */
    public function scopeDifficulty($query, ?string $nivel)
    {
        $niveles = ['muy-facil', 'facil', 'medio', 'dificil'];

        if (!in_array($nivel, $niveles)) {
            return $query;
        }

        // Filtra por coincidencia del slug normalizado
        return $query->whereRaw('LOWER(REPLACE(dificultad, " ", "-")) = ?', [$nivel]);
    }
}
