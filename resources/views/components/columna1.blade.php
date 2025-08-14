<div class="space-y-3 grid grid-cols-2 gap-2 p-2">

    <div>
        <label class="block text-sm font-medium text-gray-700" for="claseLlamada">Clase de Llamada</label>
        <input type="radio" name="claseLlamada" id="Clientes" value="Clientes" class="peer" checked>
        <label for="Clientes" class="peer-checked:border-blue-600">Clientes</label>
        <input type="radio" name="claseLlamada" id="Proveedores" value="Proveedores" class="peer">
        <label for="Proveedores" class="peer-checked:border-blue-600">Proveedores</label>
    </div>

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
        <input type="text" name="ContactCode" value="{{ old('ContactCode', $parte['ContactCode'] ?? '') }}"
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
            value="{{ old('ItemCode', $parte['ItemCode'] ?? '') }}"> {{-- producto --}}
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Descripcion</label>
        <textarea name="ItemName" id="itemNameInput"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('ItemName', $parte['ItemDescription'] ?? '') }}</textarea>{{--
        producto --}}
    </div>

    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Número de serie</label>
        <input id="itemFamilyCode" type="text" name="U_H8_SerieEurowin"
            value="{{ old('U_H8_SerieEurowin', $parte['U_H8_SerieEurowin'] ?? '') }}" {{-- producto --}}
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
</div>

<div id="sugerenciasProducto" class="absolute z-10 w-full bg-white border rounded-md shadow-lg mt-1"
    style="display: none; top: 100%;"></div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>