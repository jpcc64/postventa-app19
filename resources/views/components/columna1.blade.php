<div class="space-y-3 grid grid-cols-2 gap-2 p-2">
    <input type="hidden" name="CustomerCode" value="{{ $cliente['CardCode'] ?? '' }}">
    <input type="hidden" name="CustomerNIF" value="{{ $cliente['FederalTaxID'] ?? '' }}">
    <label class="block text-sm font-medium text-gray-700" for="claseLlamada">Clase de Llamada</label>

    <div class="col-span-2 grid grid-cols-2">
        @php
$marcarCliente = isset($cliente) && str_starts_with($cliente['CardCode'], 'C');
$marcarProveedor = isset($cliente) && !str_starts_with($cliente['CardCode'], 'C');
        @endphp
        <div>
            <input type="radio" name="ServiceBPType" id="Clientes" value="srvcSales" class="peer pointer-events-none" {{ $marcarCliente ? 'checked' : '' }} readonly>
            <label for="Clientes" class="peer-checked:border-blue-600 pointer-events-none">Clientes</label>
        </div>
        <div>
            <input type="radio" name="ServiceBPType" id="Proveedores" value="srvcPurchasing" class="peer pointer-events-none" {{ $marcarProveedor ? 'checked' : '' }} readonly>
            <label for="Proveedores" class="peer-checked:border-blue-600 pointer-events-none">Proveedores</label>
        </div>
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nombre interlocutor comercial</label>
        <input type="text" name="CustomerName" value="{{ $cliente['CardName'] ?? '' }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Persona de contacto</label>
        <input type="text" name="ContactCode" value="{{ old('ContactCode', $parte['ContactCode'] ?? '') }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Teléfono</label>
        <input type="text" name="Telephone" value="{{ old('Telephone', $cliente['Phone1'] ?? '') }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>


    <div class="col-span-2 grid grid-cols-2 relative">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Articulo
            <p class="text-xs text-gray-500">Codigo o Nombre</p>

        </label>
        <input id="itemCode" type="text" name="ItemCode"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1"
            value="{{ old('ItemCode', $parte['ItemCode'] ?? '') }}"> {{-- producto --}}
        <div id="sugerenciasProducto"
            class="absolute left-0 top-full w-full bg-white shadow-md rounded-md max-h-60 overflow-y-auto mt-1 z-10">
        </div>
    </div>

    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Descripcion</label>
        <textarea name="ItemName" id="itemNameInput"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50
             shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('ItemName', $parte['ItemDescription'] ?? '') }}</textarea>{{--
        producto --}}
    </div>
</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        let debounceTimer;
        $('#itemCode').on('input', function () {
            let query = $(this).val();

            clearTimeout(debounceTimer);

            if (query.length >= 3) {
                debounceTimer = setTimeout(function () {
                    $.ajax({
                        url: '{{ route("producto.sugerencias") }}',
                        type: 'GET',
                        data: { term: query },
                        success: function (data) {
                            let sugerencias = $('#sugerenciasProducto');
                            sugerencias.empty();

                            // Si data es un array de productos
                            if (Array.isArray(data) && data.length > 0) {
                                let lista = $('<ul class="max-h-60 overflow-y-auto"></ul>');
                                data.forEach(function (producto) {
                                    lista.append(`
                            <li class="sugerencia cursor-pointer px-4 py-2 hover:bg-blue-100 transition-all border-b border-gray-200">
                                <p class="font-semibold text-sm text-gray-800" data-id="${producto.ItemName}">${producto.ItemName}</p>
                                <p class="text-xs text-gray-500" data-id="${producto.ItemCode}">${producto.ItemCode}</p>
                            </li>
                        `);
                                });
                                sugerencias.append(lista);

                                // Evento al hacer click en sugerencia
                                $('.sugerencia').on('click', function (e) {
                                    e.preventDefault();
                                    let itemName = $(this).find('p').eq(0).text();
                                    let itemCode = $(this).find('p').eq(1).text();

                                    $('#itemCode').val(itemCode);
                                    $('#itemNameInput').val(itemName);
                                    $('#sugerenciasProducto').empty();
                                });
                            } else {
                                sugerencias.append('<div class="px-4 py-2 text-gray-500">No se encontraron productos.</div>');
                            }
                        }
                    });
                }, 400);
            } else {
                $('#sugerenciasProducto').empty();
            }
        });



        // Cerrar modal al hacer click fuera
        $(document).on('click', function (e) {
            if ($(e.target).is('#modalPartes')) {
                $('#modalPartes').addClass('hidden');
            }
        });
    });
</script>