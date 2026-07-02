@extends('layouts.app')

@section('title', 'Registrar Estudio Médico')

@section('contenido')
    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Título institucional --}}
        <h1
            class="text-2xl font-bold bg-gradient-to-r from-[#1B7D8F] via-[#2BA8A0] to-[#245360] text-transparent bg-clip-text drop-shadow-md mb-6">
            Registrar Nuevo Estudio Médico
        </h1>

        {{-- Formulario (Con enctype habilitado para recibir archivos) --}}
        <form action="{{ route('estudios_medicos.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white shadow rounded-lg p-6 border border-gray-200 space-y-6">
            @csrf

            {{-- Fila 1: Fecha y Tipo de Estudio --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha del Estudio</label>
                    <input type="date" name="fecha" id="fecha"
                        class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('fecha') is-invalid @enderror"
                        max="{{ now()->format('Y-m-d') }}" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                    @error('fecha')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_estudio" class="block text-sm font-medium text-gray-700 mb-1">Estudio / Región
                        Realizada</label>
                    <input type="text" name="tipo_estudio" id="tipo_estudio"
                        class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500 @error('tipo_estudio') is-invalid @enderror"
                        placeholder="Ej: Radiografía de Tórax, Ecografía Abdominal" value="{{ old('tipo_estudio') }}"
                        required>
                    @error('tipo_estudio')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Fila 2: Paciente y Médico Solicitante --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="paciente_id" class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                    <select name="paciente_id" id="paciente_id"
                        class="select2 w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione un paciente</option>
                        @foreach ($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                                {{ $paciente->apellido }}, {{ $paciente->nombre }} (DNI: {{ $paciente->dni }})
                            </option>
                        @endforeach
                    </select>
                    @error('paciente_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="medico_solicitante_id" class="block text-sm font-medium text-gray-700 mb-1">Médico
                        Solicitante</label>
                    <select name="medico_solicitante_id" id="medico_solicitante_id"
                        class="select2 w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione el médico</option>
                        @foreach ($medicos as $medico)
                            <option value="{{ $medico->id }}"
                                {{ old('medico_solicitante_id') == $medico->id ? 'selected' : '' }}>
                                {{ $medico->apellido }}, {{ $medico->nombre }}
                                {{ $medico->matricula ? '(M.P. ' . $medico->matricula . ')' : '(Sin Matrícula)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('medico_solicitante_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Fila 3: Resultado / Informe Clínico --}}
            <div>
                <label for="resultado" class="block text-sm font-medium text-gray-700 mb-1">Resultado / Informe Clínico
                    (Opcional)</label>
                <textarea name="resultado" id="resultado" rows="4"
                    class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                    placeholder="Escriba los resultados preliminares o definitivos del estudio...">{{ old('resultado') }}</textarea>
                @error('resultado')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- SECCIÓN MULTIMEDIA HÍBRIDA NUEVA --}}
            <div class="border-t border-gray-200 pt-6 space-y-4">
                <h3 class="text-md font-bold text-gray-700 flex items-center gap-2">
                    Archivos e Imágenes del Estudio
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Columna: Desde la PC --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subir desde la PC</label>

                        {{-- Le agregamos un ID al input para poder escucharlo desde JS (id="imagenes_locales") --}}
                        <input type="file" name="imagenes_locales[]" id="imagenes_locales" multiple accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">

                        <small class="text-gray-400 block mt-2">Podés seleccionar múltiples fotos manteniendo presionado
                            Ctrl o Shift al hacer clic.</small>

                        {{-- NUEVO CONTENEDOR PARA LAS VISTAS PREVIAS --}}
                        <div id="vista-previa-contenedor" class="grid grid-cols-3 gap-2 mt-4 hidden">
                            {{-- Las imágenes se inyectarán acá dinámicamente mediante JavaScript --}}
                        </div>

                        @error('imagenes_locales.*')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Columna: Desde Enlaces / Links --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200" id="contenedor-links">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Agregar enlaces / Links Web</label>
                        <div class="flex gap-2 mb-2">
                            <input type="url" name="imagenes_urls[]"
                                class="w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="Ej: https://drive.google.com/drive/...">
                            <button type="button" onclick="agregarCampoLink()"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md font-bold text-gray-700 border-0 cursor-pointer transition-colors">+</button>
                        </div>
                        @error('imagenes_urls.*')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-between pt-4">
                <a href="{{ route('estudios_medicos.index') }}"
                    class="btn btn-outline-danger px-5 py-2 rounded shadow-sm no-underline">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-gradient-to-r from-[#1B7D8F] to-[#2BA8A0] text-white px-6 py-2 rounded-xl font-semibold shadow-md hover:scale-105 transition duration-300">
                    Guardar Estudio
                </button>
            </div>
        </form>
    </div>
@endsection
{{-- MODAL FLOTANTE PARA VER LA IMAGEN EN GRANDE --}}
<div id="modal-visor"
    class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <button type="button" onclick="cerrarVisor()"
        class="absolute top-5 right-5 text-white hover:text-gray-300 text-3xl font-bold border-0 bg-transparent cursor-pointer transition-colors z-50">
        &times;
    </button>

    <div class="max-w-3xl max-h-[85vh] p-2 bg-white rounded-xl shadow-2xl transform scale-95 transition-transform duration-300"
        id="modal-contenido">
        <img id="imagen-grande" src="" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow">
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Seleccione una opción...'
                });
            }
        });

        // Función dinámica para agregar más casilleros de links web
        function agregarCampoLink() {
            const contenedor = document.getElementById('contenedor-links');
            const nuevoDiv = document.createElement('div');
            nuevoDiv.className = 'flex gap-2 mb-2 align-items-center';
            nuevoDiv.innerHTML = `
            <input type="url" name="imagenes_urls[]" class="w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="https://...">
            <button type="button" onclick="this.parentElement.remove()" class="px-3.5 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-bold border-0 cursor-pointer transition-colors">X</button>
        `;
            contenedor.appendChild(nuevoDiv);
        }
        $(document).ready(function() {
            // Escuchar cuando el usuario selecciona archivos del ordenador
            $('#imagenes_locales').on('change', function(event) {
                const contenedor = $('#vista-previa-contenedor');
                contenedor.empty(); // Limpiar vistas previas anteriores

                const archivos = event.target.files;

                if (archivos.length > 0) {
                    contenedor.removeClass('hidden');

                    Array.from(archivos).forEach(archivo => {
                        if (archivo.type.startsWith('image/')) {
                            const lector = new FileReader();

                            lector.onload = function(e) {
                                // Agregamos cursor-pointer y onclick="abrirVisor(this.src)" a la imagen
                                const estructuraMiniatura = `
                            <div class="relative bg-white border border-gray-200 p-1 rounded-lg shadow-sm h-20 group overflow-hidden cursor-pointer" onclick="abrirVisor('${e.target.result}')">
                                <img src="${e.target.result}" class="w-full h-full object-cover rounded group-hover:scale-105 transition-transform duration-200">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                                    <span class="text-[9px] text-white font-medium text-center px-1 truncate max-w-full">
                                        Ver grande
                                    </span>
                                </div>
                            </div>
                        `;
                                contenedor.append(estructuraMiniatura);
                            };

                            lector.readAsDataURL(archivo);
                        }
                    });
                } else {
                    contenedor.addClass('hidden');
                }
            });

            // Cerrar el modal si el usuario hace clic afuera de la imagen grande
            $('#modal-visor').on('click', function(e) {
                if (e.target === this) {
                    cerrarVisor();
                }
            });
        });

        // FUNCIÓN PARA ABRIR EL VISOR EN GRANDE (CORREGIDA)
        function abrirVisor(srcUrl) {
            const modal = document.getElementById('modal-visor');
            const modalContenido = document.getElementById('modal-contenido');
            const imagenGrande = document.getElementById('imagen-grande');

            imagenGrande.src = srcUrl;

            // 1. Quitamos la clase hidden para que el navegador renderice el modal
            modal.classList.remove('hidden');

            // 2. Le damos un mini delay para que la transición de opacidad (fade-in) funcione
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContenido.classList.remove('scale-95');
                modalContenido.classList.add('scale-100');
            }, 20);
        }

        // FUNCIÓN PARA CERRAR EL VISOR (CORREGIDA - SOLUCIONA EL TRABADO)
        function cerrarVisor() {
            const modal = document.getElementById('modal-visor');
            const modalContenido = document.getElementById('modal-contenido');

            // 1. Iniciamos la animación de desvanecido hacia afuera
            modal.classList.add('opacity-0');
            modalContenido.classList.remove('scale-100');
            modalContenido.classList.add('scale-95');

            // 2. CRUCIAL: Esperamos a que termine la animación (300ms) y le ponemos 'hidden' 
            // para destruir el escudo invisible y liberar el formulario de fondo.
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
@endpush
