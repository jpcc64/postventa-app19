<div class="space-y-3 grid grid-cols-2 gap-2 p-2">

    <div class="col-span-2 grid grid-cols-2">

        <label class="block text-sm font-medium text-gray-700 col-span-2">Código de interlocutor comercial</label>
        <input type="text" value="{{old('CustomerCode', $cliente->CardCode ?? '') }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
        <input type="hidden" name="CustomerCode" value="{{ $cliente->CardCode ?? '' }}">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nombre interlocutor comercial</label>
        <input type="text" name="CustomerName" value="{{ $cliente->CardName ?? '' }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Persona de contacto</label>
        <input type="text" name="ContactCode" value="{{ old('ContactCode', $parte->contctCode ?? '') }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Teléfono</label>
        <input type="text" name="Telephone" value="{{ old('Telephone', $cliente->Phone1 ?? '') }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Articulo</label>
        <input id="itemCodeInput" type="text" name="ItemCode"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            value="{{ old('ItemCode', $parte->itemCode ?? '') }}"> {{-- producto --}}
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Descripcion</label>
        <textarea name="ItemName" id="itemNameInput"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('ItemName', $parte->itemName ?? '') }}</textarea>{{--
        producto --}}
    </div>

    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Gama</label>
        <input id="itemFamilyCode" type="text" name="ItemGroup" value="{{ old('ItemGroup', $parte->itemGroup ?? '') }}"
            {{-- producto --}}
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
</div>

    <div id="sugerenciasProducto" class="absolute z-10 w-full bg-white border rounded-md shadow-lg mt-1"
        style="display: none; top: 100%;"></div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).ready(function () {

        // 1. Lógica para buscar sugerencias al teclear
        // Se activa al escribir en CUALQUIERA de los dos campos
        $('#itemCodeInput, #itemNameInput, #itemFamilyCode').on('input', function() {
            let query = $(this).val();
            let suggestionsContainer = $('#sugerenciasProducto');

            // Posiciona el contenedor de sugerencias debajo del campo activo
            suggestionsContainer.css({
                top: $(this).position().top + $(this).outerHeight(),
                left: $(this).position().left,
                width: $(this).outerWidth()
            });

            if (query.length < 3) {
                suggestionsContainer.empty().hide();
                return;
            }

            $.ajax({
                url: '{{ route("producto.sugerencias") }}',
                type: 'GET',
                data: {
                    codigoProducto: query
                },
                success: function (data) {
                    suggestionsContainer.empty();

                    if (data.length > 0) {
                        data.forEach(function (product) {
                            // Importante: Guardamos los datos en atributos data-*
                            let suggestionHtml = `
                            <div class="product-suggestion-item cursor-pointer p-2 hover:bg-gray-200"
                                 data-code="${product.CODIGO_PRODUCTO}"
                                 data-name="${product.NOMBRE_PRODUCTO}">
                                <strong>${product.CODIGO_PRODUCTO}</strong> - ${product.NOMBRE_PRODUCTO}
                            </div>
                        `;
                            suggestionsContainer.append(suggestionHtml);
                        });
                        suggestionsContainer.show();
                    } else {
                        suggestionsContainer.hide();
                    }
                }
            });
        });

        // 2. Lógica para cuando se hace clic en una sugerencia
        // Se usa 'event delegation' para que funcione con los elementos creados dinámicamente
        $(document).on('click', '.product-suggestion-item', function () {
            // Obtenemos los datos guardados en los atributos data-*
            let code = $(this).data('code');
            let name = $(this).data('name');
            let family = $(this).data('family') || ''; // Si no hay familia, dejamos vacío

            // Rellenamos AMBOS campos del formulario
            $('#itemCodeInput').val(code);
            $('#itemNameInput').val(name);
            $('#itemFamilyCode').val(family);

            // Limpiamos y ocultamos el contenedor de sugerencias
            $('#sugerenciasProducto').empty().hide();
        });

    });
</script>