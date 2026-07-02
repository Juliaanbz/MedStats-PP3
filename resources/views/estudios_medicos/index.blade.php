@extends('layouts.app')
@section('title', 'Gestor de Estudios Médicos')
@section('contenido')
    <div class="w-100" style="padding-left: 0; margin-left: 0;">

        {{-- Header con Título y Botón de Crear --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#1B7D8F]">
                    Gestor de Estudios Médicos
                </h1>
                <p class="text-gray-500 mt-1">Administra, visualiza y gestiona los informes clínicos de pacientes.</p>
            </div>
            <a href="{{ route('estudios_medicos.create') }}"
                class="group flex items-center gap-2 bg-[#1B7D8F] hover:bg-[#156370] text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-[#1B7D8F]/20 transition-all duration-300 transform hover:-translate-y-0.5 no-underline">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Nuevo Estudio</span>
            </a>
        </div>

        {{-- Tarjeta de Filtros y Controles --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <div class="flex flex-col lg:flex-row gap-5 justify-between items-end lg:items-center">

                {{-- Filtros de Fecha --}}
                <div class="flex flex-wrap items-end gap-4 w-full lg:w-auto">
                    <div class="w-full sm:w-auto">
                        <label for="fechaDesde"
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Desde</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            <input type="date" id="fechaDesde"
                                class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#1B7D8F] focus:border-[#1B7D8F] block w-full transition-colors">
                        </div>
                    </div>

                    <div class="w-full sm:w-auto">
                        <label for="fechaHasta"
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Hasta</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            <input type="date" id="fechaHasta"
                                class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#1B7D8F] focus:border-[#1B7D8F] block w-full transition-colors">
                        </div>
                    </div>

                    <button id="limpiarFechas"
                        class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 hover:text-[#1B7D8F] hover:border-[#1B7D8F] transition-all flex items-center gap-2 h-[38px]">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Limpiar
                    </button>
                </div>

                {{-- Buscador Global --}}
                <div class="w-full lg:w-72">
                    <label for="customSearch"
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="customSearch" placeholder="Paciente, DNI, Estudio..."
                            class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#1B7D8F] focus:border-[#1B7D8F] block w-full transition-colors">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de Resultados --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="miTabla" class="w-full text-sm text-left text-gray-500" style="width:100%">
                    <thead class="bg-[#1B7D8F] text-white uppercase">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold">DNI</th>
                            <th class="px-6 py-4 font-semibold">Paciente</th>
                            <th class="px-6 py-4 font-semibold">Estudio / Región</th>
                            <th class="px-6 py-4 font-semibold">Médico Solicitante</th>
                            <th class="px-6 py-4 font-semibold text-center no-print">Imágenes</th>
                            <th class="px-6 py-4 font-semibold text-center no-print">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($estudios as $estudio)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Fecha --}}
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                    data-fecha="{{ $estudio->fecha->format('Y-m-d') }}">
                                    {{ $estudio->fecha->format('d/m/Y') }}
                                </td>
                                {{-- DNI --}}
                                <td class="px-6 py-4 font-mono text-xs text-gray-700">
                                    {{ optional($estudio->paciente)->dni ?? '-' }}
                                </td>
                                {{-- Paciente --}}
                                <td class="px-6 py-4 font-medium text-[#1B7D8F]">
                                    {{ optional($estudio->paciente)->apellido }}, {{ optional($estudio->paciente)->nombre }}
                                </td>
                                {{-- Tipo de Estudio --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium bg-teal-50 text-teal-700 rounded-full border border-teal-100">
                                        {{ $estudio->tipo_estudio }}
                                    </span>
                                </td>
                                {{-- Médico --}}
                                <td class="px-6 py-4 text-gray-700">
                                    {{ optional($estudio->medico_solicitante)->apellido }},
                                    {{ optional($estudio->medico_solicitante)->nombre }}
                                </td>
                                {{-- Link de Imágenes Multimedia --}}
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- 1. Si tiene el Link Base Original (Drive / PACS) --}}
                                        @if ($estudio->link_imagen)
                                            <a href="{{ $estudio->link_imagen }}" target="_blank"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-100 hover:bg-teal-100 transition-colors"
                                                title="Abrir enlace principal (Nube/Visor)">
                                                <i data-lucide="link" class="w-4 h-4"></i>
                                            </a>
                                        @endif

                                        {{-- 2. Si tiene Links Adicionales o Fotos de la PC guardadas en la nueva tabla --}}
                                        @if ($estudio->imagenes->count() > 0)
                                            <a href="{{ route('estudios_medicos.show', $estudio->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-100 transition-colors"
                                                title="Este estudio tiene {{ $estudio->imagenes->count() }} elemento(s) multimedia adjunto(s). Clic para ver todos.">
                                                <i data-lucide="image" class="w-4 h-4"></i>
                                            </a>
                                        @endif

                                        {{-- 3. Si no tiene absolutamente ningún archivo ni link cargado --}}
                                        @if (!$estudio->link_imagen && $estudio->imagenes->count() == 0)
                                            <span class="text-xs text-gray-400 italic">Sin adjuntos</span>
                                        @endif

                                    </div>
                                </td>
                                {{-- Acciones --}}
                                <td class="px-6 py-4 text-center no-print">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Ver Detalles --}}
                                        <a href="{{ route('estudios_medicos.show', $estudio->id) }}"
                                            class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                            title="Ver Detalles">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        {{-- Editar --}}
                                        <a href="{{ route('estudios_medicos.edit', $estudio->id) }}"
                                            class="p-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors"
                                            title="Editar">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </a>
                                        {{-- Eliminar --}}
                                        <form action="{{ route('estudios_medicos.destroy', $estudio->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('¿Está seguro de eliminar este estudio médico?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors border-none cursor-pointer"
                                                title="Eliminar">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                    <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    No se encontraron registros de estudios médicos disponibles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .card,
            .shadow-sm {
                box-shadow: none !important;
                border: none !important;
            }
        }

        /* CORRECCIÓN PARA EL ANCHO DE LA TABLA */
        .dataTables_wrapper {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #miTabla {
            width: 100% !important;
            margin: 0 !important;
        }

        /* DataTables Custom Styling Override */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }

        /* Ocultar controles default */

        table.dataTable.no-footer {
            border-bottom: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #32989D !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e5e7eb !important;
            color: #374151 !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (window.lucide) lucide.createIcons();

            const idiomaEspanol = {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "No hay registros",
                infoFiltered: "(filtrado de _MAX_ registros)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "No hay datos disponibles",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            };

            const tabla = $('#miTabla').DataTable({
                dom: 'rt<"flex items-center justify-between px-6 py-3"ip>',
                language: idiomaEspanol,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                drawCallback: function() {
                    if (window.lucide) lucide.createIcons();
                }
            });

            $('#customSearch').on('keyup', function() {
                tabla.search(this.value).draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {

                const fechaDesde = $('#fechaDesde').val();
                const fechaHasta = $('#fechaHasta').val();

                const rowNode = $(tabla.row(dataIndex).node());
                const fechaTexto = rowNode.find('td').eq(0).data('fecha');

                if (!fechaTexto) return true;

                const fechaCirugia = new Date(fechaTexto);
                const desde = fechaDesde ? new Date(fechaDesde) : null;
                const hasta = fechaHasta ? new Date(fechaHasta) : null;

                if (desde) desde.setHours(0, 0, 0, 0);
                if (hasta) hasta.setHours(23, 59, 59, 999);

                return (!desde || fechaCirugia >= desde) && (!hasta || fechaCirugia <= hasta);
            });

            $('#fechaDesde, #fechaHasta').on('change', function() {
                tabla.draw();
            });

            $('#limpiarFechas').on('click', function() {
                $('#fechaDesde').val('');
                $('#fechaHasta').val('');
                $('#customSearch').val('');
                tabla.search('').draw();
            });

        });
    </script>
@endpush
