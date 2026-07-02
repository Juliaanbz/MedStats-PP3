<?php

namespace App\Http\Controllers;

use App\Models\EstudioMedico;
use App\Http\Requests\StoreEstudioMedicoRequest;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Empleado;

class EstudioMedicoController extends Controller
{
    /**
     * Muestra el listado de estudios médicos.
     */
    public function index()
    {
        $estudios = EstudioMedico::with(['paciente', 'medico_solicitante'])
            ->latest()
            ->get();

        return view('estudios_medicos.index', compact('estudios'));
    }

    /**
     * Muestra el formulario para registrar un nuevo estudio.
     */
    public function create()
    {
        $pacientes = Paciente::orderBy('apellido')->orderBy('nombre')->get();
        $medicos = Empleado::orderBy('apellido')->orderBy('nombre')->get();

        return view('estudios_medicos.create', compact('pacientes', 'medicos'));
    }

    /**
     * Guarda el nuevo estudio médico en la base de datos.
     */
    public function store(StoreEstudioMedicoRequest $request)
    {
        // 1. Validamos los nuevos inputs que agregamos en el Blade
        $request->validate([
            'imagenes_locales.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'imagenes_urls.*'    => 'nullable|url'
        ]);

        // 2. Guarda el estudio normalmente con tus datos existentes
        $estudio = EstudioMedico::create($request->validated());

        // 3. Procesar y guardar imágenes que subieron desde la PC
        if ($request->hasFile('imagenes_locales')) {
            foreach ($request->file('imagenes_locales') as $archivo) {
                // Guarda el archivo físico en storage/app/public/estudios
                $rutaLocal = $archivo->store('estudios', 'public');

                \App\Models\EstudioMedicoImagen::create([
                    'estudio_medico_id' => $estudio->id,
                    'ruta' => $rutaLocal,
                    'tipo' => 'local'
                ]);
            }
        }

        // 4. Procesar y guardar los múltiples links de internet adicionales
        if ($request->filled('imagenes_urls')) {
            foreach ($request->imagenes_urls as $url) {
                if (!empty($url)) {
                    \App\Models\EstudioMedicoImagen::create([
                        'estudio_medico_id' => $estudio->id,
                        'ruta' => $url,
                        'tipo' => 'url'
                    ]);
                }
            }
        }

        return redirect()->route('estudios_medicos.index')
            ->with('success', 'Estudio médico registrado correctamente con sus imágenes.');
    }

    /**
     * Muestra el detalle de un estudio médico específico.
     */
    public function show(int $id)
    {
        $estudio = EstudioMedico::with(['paciente', 'medico_solicitante', 'imagenes'])->findOrFail($id);

        return view('estudios_medicos.show', compact('estudio'));
    }

    /**
     * Muestra el formulario de edición con los datos precargados.
     */
    public function edit(EstudioMedico $estudioMedico)
    {
        $pacientes = Paciente::orderBy('apellido')->orderBy('nombre')->get();
        $medicos = Empleado::orderBy('apellido')->orderBy('nombre')->get();
        $estudio = $estudioMedico;

        return view('estudios_medicos.edit', compact('estudio', 'pacientes', 'medicos'));
    }

    /**
     * Actualiza el estudio médico en la base de datos.
     */
    public function update(Request $request, EstudioMedico $estudioMedico)
    {
        $data = $request->validate([
            'fecha'                 => 'required|date|before_or_equal:today',
            'tipo_estudio'          => 'required|string|max:255',
            'paciente_id'           => 'required|exists:pacientes,id',
            'medico_solicitante_id' => 'required|exists:empleados,id',
            'resultado'             => 'nullable|string',
            'link_imagen'           => 'nullable|url',
            'imagenes_locales.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'imagenes_urls.*'       => 'nullable|url',
            'eliminar_link_principal' => 'nullable|boolean',
            'eliminar_adjuntos'     => 'nullable|array',
            'eliminar_adjuntos.*'   => 'exists:estudio_medico_imagenes,id'
        ]);

        // --- PROCESAR ELIMINACIONES ---

        // 1. Si tildó eliminar el link principal original
        if ($request->has('eliminar_link_principal')) {
            $data['link_imagen'] = null;
        }

        // 2. Si tildó eliminar adjuntos de la tabla secundaria
        if ($request->has('eliminar_adjuntos')) {
            foreach ($request->eliminar_adjuntos as $idImagen) {
                $imagen = \App\Models\EstudioMedicoImagen::find($idImagen);
                if ($imagen) {
                    // Si es un archivo local físico, lo borramos del disco duro
                    if ($imagen->tipo === 'local') {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($imagen->ruta);
                    }
                    // Borramos el registro de la base de datos
                    $imagen->delete();
                }
            }
        }

        // Actualizamos los datos principales del estudio
        $estudioMedico->update($data);

        // --- PROCESAR NUEVOS VALORES INGRESADOS (Igual a lo de antes) ---

        if ($request->hasFile('imagenes_locales')) {
            foreach ($request->file('imagenes_locales') as $archivo) {
                $rutaLocal = $archivo->store('estudios', 'public');
                \App\Models\EstudioMedicoImagen::create([
                    'estudio_medico_id' => $estudioMedico->id,
                    'ruta' => $rutaLocal,
                    'tipo' => 'local'
                ]);
            }
        }

        if ($request->filled('imagenes_urls')) {
            foreach ($request->imagenes_urls as $url) {
                if (!empty($url)) {
                    \App\Models\EstudioMedicoImagen::create([
                        'estudio_medico_id' => $estudioMedico->id,
                        'ruta' => $url,
                        'tipo' => 'url'
                    ]);
                }
            }
        }

        return redirect()->route('estudios_medicos.index')
            ->with('success', 'El estudio médico y sus archivos adjuntos se actualizaron correctamente.');
    }

    /**
     * Elimina el estudio médico de la base de datos.
     */
    public function destroy(EstudioMedico $estudioMedico)
    {
        $estudioMedico->delete();

        return redirect()->route('estudios_medicos.index')
            ->with('success', 'Estudio médico eliminado correctamente.');
    }
}
