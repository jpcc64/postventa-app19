@include('components.head')
<h1 class=" text-3xl font-bold text-center text-gray-800 mb-6">Crear llamada de servicio</h1>

<a href="{{ route('home') }}">
    <button type="button" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 mx-3 rounded mb-4">
        Volver
    </button>
</a>

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
    <div class="flex space-x-3 mb-4">

        <form action="{{ route('parte.buscar') }}" method="get" class="w-2/3">
            @csrf
            <div class="flex items-center space-x-4 relative w-full">
                <input type="text" id="busquedaCliente" name="buscar"
                    class="form-control p-3 border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none transition duration-300 w-full"
                    placeholder="Buscar cliente por nombre, CIF o teléfono" autocomplete="on">

                <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6" type="submit">
                    Buscar
                </button>
                <div id="sugerencias"
                    class="absolute top-full left-0 bg-white shadow-md rounded-md z-50 max-h-60 overflow-y-auto w-full mt-1">
                </div>
            </div>
        </form>

        <!-- <form action="{{ route('parte.buscarRMA') }}" method="get" class="w-1/3">
            @csrf
            <div class="flex items-center space-x-4 relative w-full">
                <input type="text" id="busquedaRMA" name="busquedaRMA"
                    class="form-control p-3 border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none transition duration-300 w-full"
                    placeholder="Buscar por RMA" autocomplete="on">

                <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6" type="submit">
                    Buscar
                </button>
            </div>
        </form> -->
    </div>
</div>

@if(isset($cliente) && isset($parte))
    <a href="{{ route('partes.imprimir', ['id' => $parte['ServiceCallID'], 'cliente' => $cliente['CardCode']]) }}" target="_blank">
        <button type="button" class="mb-4 bg-amber-600 hover:bg-amber-700 text-white rounded-lg py-3 px-6 mx-3">Imprimir Parte</button>
    </a>
    <a href="{{ route('parte.nuevo', $cliente['CardCode']) }}">
        <button type="button" class="mb-4 bg-green-600 hover:bg-green-700 text-white rounded-lg py-3 px-6 mx-3">Nuevo parte</button>
    </a>
    @include('components.btn_aviso')
@endif

@if(isset($partes))
    <div x-data="{ filter: '' }">
        <h3 class="text-lg font-semibold mb-2 underline text-center col-span-3">Selecciona el parte</h3>
        
        <!-- FILTRO DE BÚSQUEDA en el frontend -->
        <div class="my-4 max-w-5xl mx-auto">
            <label for="filtroParte" class="block text-sm font-medium text-gray-700">Filtrar por ID de parte o nombre del cliente o producto: </label>
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
            @foreach($partes as $parte)
                <div class="col-span-1"
                     x-show="filter === '' || '{{ $parte['ServiceCallID'] }}'.toLowerCase().includes(filter.toLowerCase()) || '{{ addslashes($parte['U_H8_Nombre']) }}'.toLowerCase().includes(filter.toLowerCase()) || '{{ addslashes($parte['ItemDescription']) }}'.toLowerCase().includes(filter.toLowerCase())">
                    <div class="p-6 border border-gray-300 rounded-xl shadow-md flex flex-col h-full">
                        <div class="flex-grow">
                             <h3 class="text-lg font-semibold mb-2">Parte #{{ $parte['ServiceCallID'] }}</h3>
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
                        <form method="GET" action="{{ route('parte.formulario', $parte['ServiceCallID']) }}" class="mt-4">
                            <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 rounded-lg">
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
    <form id="form-parte" method="POST" action="{{ route('parte.crear') }}" enctype="multipart/form-data">
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
    $(document).ready(function () {
        let debounceTimer;
        $('#busquedaCliente').on('input', function () {
            let query = $(this).val();
            let sugerencias = $('#sugerencias');
            clearTimeout(debounceTimer);
            if (query.length >= 4) {
                debounceTimer = setTimeout(function () {
                    $.ajax({
                        url: '{{ route("buscar.sugerencias") }}',
                        type: 'GET',
                        data: { term: query },
                        success: function (data) {
                            sugerencias.empty();
                            if (data.length > 0) {
                                data.forEach(function (item) {
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

        $(document).on('click', '.sugerencia', function (e) {
            e.preventDefault();
            let cardCode = $(this).data('id');
            $('#busquedaCliente').val(cardCode);
            $('#sugerencias').empty();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#busquedaCliente, #sugerencias').length) {
                $('#sugerencias').empty();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const formParte = document.getElementById('form-parte');
        if (!formParte) {
            return; 
        }

        const statusSelect = formParte.querySelector('select[name="Status"]');
        if (statusSelect && statusSelect.value == '-1') {
            const fieldsToDisable = formParte.querySelectorAll('input, textarea, select');
            fieldsToDisable.forEach(function (field) {
                if (field !== statusSelect) {
                    field.classList.add('bg-gray-200');
                    field.setAttribute('readonly', true);
                    if (field.tagName === 'SELECT') {
                        field.setAttribute('readonly', true);
                    }
                }
            });
        }
   document.querySelectorAll('form').forEach(form => {
        form.addEventListener('keydown', function(event) {
            // Si la tecla presionada es 'Enter'
            if (event.key === 'Enter') {
                event.preventDefault();

                const focusableElements = Array.from(
                    form.querySelectorAll('input, select, textarea, button, a[href]')
                ).filter(
                    el => !el.disabled && !el.hidden && el.type !== 'hidden' && window.getComputedStyle(el).display !== 'none'
                );
                const currentIndex = focusableElements.indexOf(event.target);
                const nextIndex = currentIndex + 1;

                if (nextIndex < focusableElements.length) {
                    focusableElements[nextIndex].focus();
                }
            }
        });
    });
    });

</script>

@include('components.footer')

