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
            class="ml-4 text-green-700 hover:text-green-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif

{{-- Muestra errores de validación o de la API --}}
@if ($errors->any())
    <div id="alertaErrores"
        class="max-w-xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-mdr"
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
            class="ml-4 text-green-700 hover:text-green-900 focus:outline-none">
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
                    placeholder="Buscar cliente" autocomplete="on">

                <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6" type="submit">
                    Buscar
                </button>
                <div id="sugerencias"
                    class="absolute top-full left-0 bg-white shadow-md rounded-md z-50 max-h-60 overflow-y-auto w-full mt-1">
                </div>
            </div>

        </form>
        <form action="{{ route('parte.buscarRMA') }}" method="get" class="w-1/3">

            @csrf
            <div class="flex items-center space-x-4 relative w-full">
                <input type="text" id="busquedaRMA" name="busquedaRMA"
                    class="form-control p-3 border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none transition duration-300 w-full"
                    placeholder="Buscar RMA" autocomplete="on">

                <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6" type="submit">
                    Buscar
                </button>
            </div>

        </form>

    </div>
</div>
@if(isset($cliente) && isset($parte))
    <a href="{{ route('partes.imprimir', ['id' => $parte['ServiceCallID'], 'cliente' => $cliente['CardCode']]) }}" target="_blank">
        <button type="button" class="mb-4 bg-amber-600 hover:bg-amber-700 text-white rounded-lg py-3 px-6 mx-3">Imprimir
            Parte</button>
    </a>
    <a href="{{ route('parte.nuevo', $cliente['CardCode']) }}">
        <button type="button" class="mb-4 bg-green-600 hover:bg-green-700 text-white rounded-lg py-3 px-6 mx-3">
            Nuevo parte</button>
    </a>
    
       @include('components.btn_aviso')
@endif
@if(isset($partes))

    <div>
        <h3 class="text-lg font-semibold mb-2 underline text-center col-span-3">Selecciona el parte</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- si quiere crear uno desde 0 solo rellena la primera columna -->
            <div class="col-span-1">
                <div class="p-4 border border-gray-300 rounded grid shadow mb-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold mb-2">Crear nuevo parte</h3>
                        <p>Si no encuentras el parte que buscas, puedes crear uno nuevo.</p>
                    </div>
                    <a href="{{ route('parte.nuevo', $cliente['CardCode']) }}"
                        class="block bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 mx-3 mt-4 rounded w-1/4">
                        Crear Parte
                    </a>
                </div>
            </div>
            @foreach($partes as $parte)
                <div class="col-span-1">
                    <div class="p-6 border border-gray-300 rounded grid shadow mb-6">
                        <h3 class="text-lg font-semibold mb-2">Parte #{{ $parte['ServiceCallID'] }}</h3>
                        @switch($parte['Status'] ?? '')
                        @case('-3')
                            <strong class="text-green-500">Abierto</strong>
                            @break
                        @case('-2')
                            <strong class="text-yellow-500">Pendiente</strong>
                            @break
                        @case('-1')
                            <strong class="text-red-500">Cerrado</strong>
                            @break
                        @default
                            <span>N/A</span>
                    @endswitch
                        <p>{{ $parte['U_H8_Nombre'] ?? $parte['CustomerName'] }}</p>
                        <p>{{ $parte['ItemDescription'] }}</p>
                        <form method="GET" action="{{ route('parte.formulario', $parte['ServiceCallID']) }}">
                            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 mx-3 rounded mb-4">
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
<div class="bg-white p-6 sm:p-8 rounded-xl shadow-md border border-slate-200">
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


        $(document).on('click', '.parte-btn', function () {
            let callID = $(this).data('callid');
            console.log(callID);
            window.location.href = '/parte/formulario/' + callID;
        });

        $(document).on('click', function (e) {
            if ($(e.target).is('#sugerencias')) {
                $('#sugerencias').addClass('hidden');
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
                    field.classList.add('bg-gray-300');
                    field.readOnly = true;
                }
            });
        }
    });
    
</script>