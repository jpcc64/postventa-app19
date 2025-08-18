<div class="space-y-3 grid grid-cols-2 gap-2 p-2">
    <label class="block text-sm font-medium text-gray-700" for="claseLlamada">Clase de Llamada</label>

    <div class="col-span-2 grid grid-cols-2">
        <div>
            <input type="radio" name="claseLlamada" id="Clientes" value="srvcSales" class="peer" {{ (isset($parte['ServiceBPType']) && $parte['ServiceBPType'] == 'srvcSales') ? 'checked' : '' }}>
            <label for="Clientes" class="peer-checked:border-blue-600">Clientes</label>
        </div> 
        <div>
            <input type="radio" name="claseLlamada" id="Proveedores" value="Proveedores" class="peer" {{ (isset($parte['ServiceBPType']) && $parte['ServiceBPType'] == 'srvcPurchasing') ? 'checked' : '' }}>
            <label for="Proveedores" class="peer-checked:border-blue-600">Proveedores</label>
        </div>
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nombre interlocutor comercial</label>
        <input type="text" name="CustomerName" value="{{ $cliente['CardName'] ?? '' }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Persona de contacto</label>
        <input type="text" name="ContactCode" value="{{ old('ContactCode', $parte['ContactCode'] ?? '') }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Teléfono</label>
        <input type="text" name="Telephone" value="{{ old('Telephone', $cliente['Phone1'] ?? '') }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>


    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Articulo</label>
        <input id="itemCode" type="text" name="ItemCode"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            value="{{ old('ItemCode', $parte['ItemCode'] ?? '') }}"> {{-- producto --}}
    </div>
    <div id="sugerenciasProducto"
        class="absolute top-full right-10 bg-white shadow-md rounded-md z-50 max-h-60 overflow-y-auto w-full mt-1">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Descripcion</label>
        <textarea name="ItemName" id="itemNameInput"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50
             shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('ItemName', $parte['ItemDescription'] ?? '') }}</textarea>{{--
        producto --}}
    </div>

    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Número de serie</label>
        <input id="itemFamilyCode" type="text" name="InternalSerialNum"
            value="{{ old('InternalSerialNum', $parte['InternalSerialNum'] ?? '') }}" {{-- producto --}}
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('#itemCode').on('input', function () {
            let query = $(this).val();

            if (query.length >= 4) {
                $.ajax({
                    url: '{{ route("producto") }}',
                    type: 'GET',
                    data: { term: query },
                    success: function (data) {
                        let sugerencias = $('#sugerenciasProducto');
                        sugerencias.empty();
                        sugerencias.append(`
                                    <div class="sugerencia cursor-pointer px-4 py-2 hover:bg-blue-100 transition-all border-b border-gray-200">
                                        <p data-id="${data.ItemCode}">${data.ItemCode}</p>
                                        <p data-id="${data.ItemName}">${data.ItemName}</p>
                                    </div>
                                `);
                                                        console.log('exito: ', data.ItemName);

                        // Evento al hacer click en sugerencia
                        $('.sugerencia').on('click', function (e) {
                            e.preventDefault();
                            let cardCode = $(this).data('id');
                            $('#itemCode').val(cardCode);
                            $('#sugerenciasProducto').empty();
                        });

                    }
                });
            } else {
                console.log('error: ', data);
                $('#sugerencias').empty();
            }
        });

        // Click en una parte del modal
        $(document).on('click', '.parte-btn', function () {
            let callID = $(this).data('callid');
            console.log(callID);
            window.location.href = '/parte/formulario/' + callID;
        });

        // Cerrar modal al hacer click fuera
        $(document).on('click', function (e) {
            if ($(e.target).is('#modalPartes')) {
                $('#modalPartes').addClass('hidden');
            }
        });
    });
</script>