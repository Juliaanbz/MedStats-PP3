<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstudioMedico;
use App\Models\Paciente;
use App\Models\Empleado;
use Carbon\Carbon;

class EstudioMedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos el primer paciente y el primer médico de la base de datos
        $paciente = Paciente::first();
        $medico = Empleado::first();

        // Si no existen registros base, te avisará en la consola antes de fallar
        if (!$paciente || !$medico) {
            $this->command->error('No se encontraron pacientes o empleados en la base de datos. Crea al menos uno antes de correr este seeder.');
            return;
        }

        // Insertamos datos de prueba vinculados a ellos
        EstudioMedico::create([
            'paciente_id' => $paciente->id,
            'medico_solicitante_id' => $medico->id,
            'tipo_estudio' => 'Radiografía de Tórax',
            'fecha' => Carbon::now()->subDays(5),
            'resultado' => 'Estructuras óseas conservadas. Campos pulmonares limpios sin infiltrados.',
        ]);

        EstudioMedico::create([
            'paciente_id' => $paciente->id,
            'medico_solicitante_id' => $medico->id,
            'tipo_estudio' => 'Laboratorio Clínico Completo',
            'fecha' => Carbon::now()->subDays(2),
            'resultado' => 'Hemograma completo dentro de los parámetros normales. Glucemia en ayunas: 95 mg/dL.',
        ]);

        $this->command->info('¡Seeder de Estudios Médicos ejecutado con éxito!');
    }
}