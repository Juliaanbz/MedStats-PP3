@extends('layouts.app')
@section('title', 'Ver Estudio Médico')
@section('contenido')
    <div class="max-w-4xl mx-auto px-6 py-8">

        {{-- Título --}}
        <h1
            class="text-2xl font-bold bg-gradient-to-r from-[#1B7D8F] via-[#2BA8A0] to-[#245360] text-transparent bg-clip-text drop-shadow mb-6">
            Detalle del Estudio Médico
        </h1>

        {{-- Tarjeta principal --}}
        <div class="bg-white shadow rounded-lg p-6 border border-gray-200 space-y-5 text-[15px]">

            {{-- Paciente --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 uppercase tracking-wide text-sm">Paciente</p>
                    <p class="font-semibold text-gray-800">
                        {{ optional($estudio->paciente)->apellido }}, {{ optional($estudio->paciente)->nombre }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 uppercase tracking-wide text-sm">DNI</p>
                    <p class="font-semibold text-gray-800">{{ optional($estudio->paciente)->dni ?? '-' }}</p>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Tipo de Estudio y Médico --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 uppercase text-sm">Estudio / Región Realizada</p>
                    <p class="text-gray-800 font-medium">{{ $estudio->tipo_estudio }}</p>
                </div>
                <div>
                    <p class="text-gray-500 uppercase text-sm">Médico Solicitante</p>
                    <p class="text-gray-800 font-medium">
                        {{ optional($estudio->medico_solicitante)->apellido }},
                        {{ optional($estudio->medico_solicitante)->nombre }}
                        <span class="text-gray-400 text-xs block">
                            {{ optional($estudio->medico_solicitante)->matricula ? '(M.P. ' . optional($estudio->medico_solicitante)->matricula . ')' : '(Sin Matrícula)' }}
                        </span>
                    </p>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Informe Clínico --}}
            <div>
                <p class="text-gray-500 uppercase text-sm mb-2">Resultado / Informe Clínico</p>
                <div
                    class="bg-gray-50 rounded-lg p-4 border border-gray-100 font-mono text-sm text-gray-700 whitespace-pre-wrap">
                    {{ $estudio->resultado ?: 'Sin informe cargado preliminarmente.' }}
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- SECCIÓN DE DOCUMENTOS ADJUNTOS / GALERÍA REAL --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="text-gray-500 uppercase text-sm mb-3 font-semibold">Imágenes y Documentos Vinculados</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">

                    {{-- 1. Mostrar el link de imagen viejo de la tabla principal si existe --}}
                    @if ($estudio->link_imagen)
                        <div class="flex flex-col justify-between p-3 bg-teal-50 border border-teal-100 rounded-xl">
                            <p class="text-xs font-bold text-teal-800 m-0 uppercase">Enlace Base</p>
                            <a href="{{ $estudio->link_imagen }}" target="_blank"
                                class="text-xs text-blue-600 truncate my-2 block hover:underline">
                                {{ $estudio->link_imagen }}
                            </a>
                            <a href="{{ $estudio->link_imagen }}" target="_blank"
                                class="w-full text-center py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs rounded no-underline font-medium">
                                Abrir Enlace
                            </a>
                        </div>
                    @endif

                    {{-- 2. Recorrer la nueva colección de múltiples imágenes guardadas --}}
                    @forelse($estudio->imagenes as $img)
                        <div
                            class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm flex flex-col justify-between group relative">
                            @if ($img->tipo === 'local')
                                {{-- Si se subió de la PC, mostramos una miniatura real de la imagen --}}
                                <div class="p-1 bg-gray-50 flex justify-center items-center h-28 overflow-hidden">
                                    <img src="{{ asset('storage/' . $img->ruta) }}"
                                        class="h-full w-full object-cover rounded cursor-pointer group-hover:scale-105 transition"
                                        onclick="window.open(this.src, '_blank')">
                                </div>
                                <div class="p-2 border-t border-gray-100 flex justify-between items-center bg-gray-50">
                                    <span class="text-[11px] text-gray-400">Adjunto PC</span>
                                    <a href="{{ asset('storage/' . $img->ruta) }}" download
                                        class="text-[11px] text-blue-600 font-bold no-underline hover:underline">Descargar</a>
                                </div>
                            @else
                                {{-- Si es un link web, mostramos un acceso directo elegante --}}
                                <div class="p-3 h-28 flex flex-col justify-between bg-blue-50/50">
                                    <p class="text-[11px] font-bold text-blue-800 m-0 uppercase tracking-wide">Link
                                        Adicional</p>
                                    <p class="text-xs text-gray-500 truncate my-1">{{ $img->ruta }}</p>
                                    <a href="{{ $img->ruta }}" target="_blank"
                                        class="w-full text-center py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] rounded no-underline font-medium mt-1">
                                        Visitar Enlace
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        @if (!$estudio->link_imagen)
                            <div
                                class="col-span-full p-4 bg-gray-50 rounded-xl text-center border border-dashed border-gray-200 text-gray-400 text-sm">
                                <i class="bi bi-images block text-xl mb-1"></i>
                                No se encontraron imágenes ni enlaces multimedia cargados para este estudio.
                            </div>
                        @endif
                    @endforelse

                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Datos de Control Temporales --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 uppercase text-sm">Fecha del Estudio</p>
                    <p class="text-gray-800 font-medium">
                        {{ $estudio->fecha ? $estudio->fecha->format('d/m/Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 uppercase text-sm">Última Actualización</p>
                    <p class="text-gray-800 font-medium">
                        {{ $estudio->updated_at->format('d/m/Y H:i') }} hs
                    </p>
                </div>
            </div>

            {{-- Acciones del Footer --}}
            <div class="flex justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('estudios_medicos.index') }}"
                    class="btn btn-outline-danger px-5 py-2 rounded shadow-sm no-underline">
                    Volver al Listado
                </a>
                <a href="{{ route('estudios_medicos.edit', $estudio->id) }}"
                    class="bg-gradient-to-r from-[#1B7D8F] to-[#2BA8A0] text-white px-5 py-2 rounded shadow-sm font-semibold hover:scale-105 transition duration-300 no-underline">
                    Editar Estudio
                </a>
            </div>
        </div>
    </div>
@endsection
