<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes'; // Forzamos el plural correcto en español
    protected $fillable = ['estudio_medico_id', 'ruta', 'tipo'];

    public function estudio_medico()
    {
        return $this->belongsTo(EstudioMedico::class, 'estudio_medico_id');
    }
}