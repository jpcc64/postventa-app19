<div class="space-y-1 grid grid-cols-2 gap-2 rounded-md">
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nombre</label>
        <input type="text" name="U_H8_Nombre" value="{{ old('U_H8_Nombre', $parte['U_H8_Nombre'] ?? '') }}"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nºteléfono</label>
        <input type="text" name="U_H8_Telefono" value="{{ old('U_H8_Telefono', $parte['U_H8_Telefono'] ?? '') }}"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Asunto</label>
        <input type="text" name="Subject" class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            value="{{ old('Subject', $parte['Subject'] ?? '') }}">
    </div>
        <div class="col-span-2 grid grid-cols-2">
            <label class="block text-sm font-medium text-gray-700 col-span-2">Número de serie Eurowin</label>
            <input id="itemFamilyCode" type="text" name="U_H8_SerieEurowin"
                value="{{ old('U_H8_SerieEurowin', $parte['U_H8_SerieEurowin'] ?? '') }}" {{-- producto --}}
                class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">R.M.A</label>
        <input type="text" name="U_H8_RMA" class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            value="{{ old('U_H8_RMA', $parte['U_H8_RMA'] ?? '') }}">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Tipo de llamada</label>
        <select name="U_H8_MOTIVO" id="" class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <option value="" selected >Selecciona un motivo</option>
            <option value="ST" {{ (isset($parte['U_H8_MOTIVO']) && $parte['U_H8_MOTIVO'] == 'ST') ? 'selected' : '' }}>Servicio técnico</option>
            <option value="DEV" {{ (isset($parte['U_H8_MOTIVO']) && $parte['U_H8_MOTIVO'] == 'DEV') ? 'selected' : '' }}>Proveedor</option>
        </select>

    </div>
</div>