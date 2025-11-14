@include('components.head')
<h1 class=" text-3xl font-bold text-center text-gray-800 mb-6">Crear llamada de servicio</h1>
<div class="flex items-center justify-center mb-4 space-x-4">
    <a href="{{ route('home') }}">
        <button type="button" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded flex-none">
            Volver
        </button>
    </a>
    <img src="{{ asset('storage/Logo_elec_euro_R.png') }}" alt="Logo" class="h-16 justify-center mx-auto mb-4">
</div>

{{-- Bloque para mostrar mensajes de éxito y error --}}
@if (session('success'))
    <div id="alertaSuccess"
        class="max-w-xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-md"
        role="alert">
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('alertaSuccess').style.display='none';" type="button"
            class="ml-4 text-green-700 hover:text-green-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div id="alertaError"
        class="max-w-xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-md"
        role="alert">
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('alertaError').style.display='none';" type="button"
            class="ml-4 text-red-700 hover:text-red-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif

{{-- Muestra errores de validación o de la API --}}
@if ($errors->any())
    <div id="alertaErrores"
        class="max-w-xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-md"
        role="alert">
        <div>
            <strong>¡Ups! Ocurrió un error: </strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="document.getElementById('alertaErrores').style.display='none';" type="button"
            class="ml-4 text-red-700 hover:text-red-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif


<div class="max-w-5xl mx-auto">
    <div class="flex justify-center items-center space-x-3 mb-4">
        <form action="{{ route('parte.buscar') }}" method="get" class="w-full max-w-lg">
            @csrf
            <div class="flex items-center space-x-4 relative w-full justify-center">
                <input type="text" id="busquedaCliente" name="buscar"
                    class="form-control p-3 border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none transition duration-300 w-full"
                    placeholder="Buscar cliente por nombre, CIF o teléfono" autocomplete="on">
                <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6" type="submit">
                    Buscar
                </button>
                <div id="sugerencias"
                    class="absolute right-0 top-full bg-white shadow-md rounded-md z-50 max-h-60 overflow-y-auto w-full mt-1">
                </div>
            </div>
        </form>
    </div>
</div>

@if (isset($cliente) && isset($parte))

    <a href="{{ route('partes.imprimir', ['id' => $parte['ServiceCallID'], 'cliente' => $cliente['CardCode']]) }}"
        target="_blank">
        <button type="button"
            class="mb-4 bg-amber-600 hover:bg-amber-700 text-white rounded-lg py-3 px-6 mx-3">Imprimir Parte</button>
    </a>
    <a href="{{ route('parte.nuevo', $cliente['CardCode']) }}">
        <button type="button" class="mb-4 bg-green-600 hover:bg-green-700 text-white rounded-lg py-3 px-6 mx-3">Nuevo
            parte</button>
    </a>
    @include('components.btn_aviso')
@endif

@if (isset($partes))
    <div x-data="{ filter: '' }">
        <h3 class="text-lg font-semibold mb-2 underline text-center col-span-3">Selecciona el parte</h3>

        <!-- FILTRO DE BÚSQUEDA en el frontend -->
        <div class="my-4 max-w-5xl mx-auto">
            <label for="filtroParte" class="block text-sm font-medium text-gray-700">Filtrar por ID de parte o nombre
                del cliente o producto: </label>
            <input type="text" id="filtroParte" x-model="filter" placeholder="Escribe para filtrar..."
                class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 max-w-5xl mx-auto">
            <!-- Card para crear nuevo parte -->
            <div class="col-span-1">
                <div class="p-4 border border-gray-300 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-lg font-bold mb-2">Crear nuevo parte</h3>
                        <p>Si no encuentras el parte que buscas, puedes crear uno nuevo para este cliente.</p>
                    </div>
                    <a href="{{ route('parte.nuevo', $cliente['CardCode']) }}"
                        class="block text-center bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 mt-4 rounded-lg self-start">
                        Crear Parte
                    </a>
                </div>
            </div>

            <!-- Listado de partes existentes -->
            @foreach ($partes as $parte)
                @php
                    $nombreOrigen = '';
                    foreach ($origenes as $origen) {
                        if ($origen['OriginID'] == $parte['Origin']) {
                            $nombreOrigen = $origen['Name'];
                            break;
                        }
                    }
                @endphp
                <div class="col-span-1"
                    x-show="filter === '' || '{{ $parte['DocNum'] }}'.toLowerCase().includes(filter.toLowerCase()) ||
                     '{{ $parte['CreationDate'] }}'.toLowerCase().includes(filter.toLowerCase()) ||
                      '{{ addslashes($parte['U_H8_Nombre']) }}'.toLowerCase().includes(filter.toLowerCase()) ||
                       '{{ addslashes($parte['ItemDescription']) }}'.toLowerCase().includes(filter.toLowerCase()) ||
                        '{{ addslashes($nombreOrigen) }}'.toLowerCase().includes(filter.toLowerCase())">
                    <div class="p-6 border border-gray-300 rounded-xl shadow-md flex flex-col h-full">
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold mb-2">Parte #{{ $parte['DocNum'] }}</h3>
                            <p>

                                Origen: {{ $nombreOrigen }}
                            </p>
                            <p>Fecha: {{ $parte['CreationDate'] }} </p>
                            @switch($parte['Status'] ?? '')
                                @case('-3')
                                    <strong class="text-green-600">Abierto</strong>
                                @break

                                @case('-2')
                                    <strong class="text-yellow-600">Pendiente</strong>
                                @break

                                @case('-1')
                                    <strong class="text-red-600">Cerrado</strong>
                                @break

                                @default
                                    <span>N/A</span>
                            @endswitch
                            <p class="mt-2">{{ $parte['U_H8_Nombre'] ?? $parte['CustomerName'] }}</p>
                            <p class="text-sm text-gray-600">{{ $parte['ItemDescription'] }}</p>
                        </div>
                        <form method="GET" action="{{ route('parte.formulario', $parte['ServiceCallID']) }}"
                            class="mt-4">
                            @csrf
                            <button type="submit"
                                class="w-full bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 rounded-lg">
                                Seleccionar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- 🔹 Formulario completo -->
<div class="bg-white p-6 sm:p-8 rounded-xl shadow-md border border-slate-200 mx-auto">
    <form id="form-parte" method="POST" action="{{ route('parte.crear') }}">
      @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-8">
            @include('components.columna1')
            @include('components.columna2')
            @include('components.columna3')
            <div class="md:col-span-3">
                @include('components.pestañasForm')
            </div>
        </div>
    </form>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Lógica que usa jQuery
    $(document).ready(function() {
        let debounceTimer;
        $('#busquedaCliente').on('input', function() {
            let query = $(this).val();
            let sugerencias = $('#sugerencias');
            clearTimeout(debounceTimer);
            if (query.length >= 4) {
                debounceTimer = setTimeout(function() {
                    $.ajax({
                        url: '{{ route('buscar.sugerencias') }}',
                        type: 'GET',
                        data: {
                            term: query
                        },
                        success: function(data) {
                            sugerencias.empty();
                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    sugerencias.append(`
                                    <div class="sugerencia cursor-pointer px-4 py-2 hover:bg-blue-100" data-id="${item.CardCode}">
                                        <div class="font-semibold text-sm text-gray-800">${item.CardName}</div>
                                        <div class="text-xs text-gray-500">${item.CardCode} - ${item.Phone1}</div>
                                    </div>
                                    `);
                                });
                            }
                        }
                    });
                }, 400);
            } else {
                sugerencias.empty();
            }
        });

        $(document).on('click', '.sugerencia', function(e) {
            e.preventDefault();
            let cardCode = $(this).data('id');
            $('#busquedaCliente').val(cardCode);
            $('#sugerencias').empty();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#busquedaCliente, #sugerencias').length) {
                $('#sugerencias').empty();
            }
        });
    });


    // Lógica que usa JavaScript puro (Vanilla JS)
    document.addEventListener('DOMContentLoaded', function() {

        // --- LÓGICA PARA HABILITAR/DESHABILITAR CAMPOS SEGÚN EL ESTADO DEL PARTE ---
        const formParte = document.getElementById('form-parte');
        if (formParte) {
            const statusSelect = formParte.querySelector('select[name="Status"]');

            const toggleFormFields = (disable) => {
                // Seleccionamos todos los elementos de formulario que se puedan deshabilitar
                const fieldsToToggle = formParte.querySelectorAll('input, textarea, select');
                
                fieldsToToggle.forEach(field => {
                    // Excepciones: no deshabilitar el selector de estado ni el token CSRF.
                    if (field.name === 'Status' || field.name === '_token') {
                        return; 
                    }
                    
                    if (disable) {
                        field.setAttribute('readonly', true);
                    } else {
                        field.removeAttribute('readonly');
                    }
                });
            };

            // Comprobar el estado inicial al cargar la página
            if (statusSelect && statusSelect.value == '-1') {
                toggleFormFields(true);
            }

            // Añadir un listener para reaccionar a los cambios en el estado
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    toggleFormFields(this.value == '-1');
                });
            }
        }

        // --- LÓGICA PARA PREVENIR ENVÍO CON "ENTER" ---
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('keydown', function(event) {
                if (event.key !== 'Enter') {
                    return;
                }

                const targetElement = event.target;
                const tagName = targetElement.tagName.toLowerCase();

                // Permitir 'Enter' en textareas y botones de submit
                if (tagName === 'textarea' || targetElement.type === 'submit') {
                    return;
                }

                // Prevenir envío y mover el foco en los demás casos
                event.preventDefault();

                const focusableElements = Array.from(
                    form.querySelectorAll(
                        'input:not([type="hidden"]), select, textarea, button, a[href]')
                ).filter(
                    el => !el.disabled && el.offsetParent !== null
                );

                const currentIndex = focusableElements.indexOf(targetElement);
                const nextElement = focusableElements[currentIndex + 1];

                if (nextElement) {
                    nextElement.focus();
                }
            });
        });
    });
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
</script>


@include('components.footer')
