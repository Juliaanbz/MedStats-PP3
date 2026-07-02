<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudioMedicoImagen extends Model
{
    protected $table = 'estudio_medico_imagenes';
    protected $fillable = ['estudio_medico_id', 'ruta', 'tipo'];
}