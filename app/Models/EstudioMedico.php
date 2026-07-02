<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstudioMedico extends Model
{
    use HasFactory;

    protected $table = 'estudio_medicos';

    // Mantenemos tus fillable intactos, pero recordá que ahora 'link_imagen' no será el único
    protected $fillable = [
        'paciente_id',
        'tipo_estudio',
        'fecha',
        'resultado',
        'medico_solicitante_id',
        'link_imagen', // Lo dejamos por compatibilidad si tenés datos viejos
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * RELACIÓN AGREGADA: Obtiene todas las imágenes/links del estudio
     */
    public function get_imagenes()
    {
        return $this->hasMany(\Illuminate\Support\Facades\DB::table('estudio_medico_imagenes')->getProcess() ? null : \App\Models\EstudioMedicoImagen::class, 'estudio_medico_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico_solicitante()
    {
        return $this->belongsTo(Empleado::class, 'medico_solicitante_id');
    }

    public function imagenes()
    {
        return $this->hasMany(EstudioMedicoImagen::class, 'estudio_medico_id');
    }
}
