<div class="space-y-3 grid grid-cols-2 gap-2 p-2">

    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nombre (Contado)</label>
        <input type="text" name="U_H8_Nombre" value="{{ old('U_H8_Nombre', $parte['U_H8_Nombre'] ?? '') }}"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>

    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nºteléfono (Contado)</label>
        <input type="text" name="U_H8_Telefono" value="{{ old('U_H8_Telefono', $parte['U_H8_Telefono'] ?? '') }}"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">NIF (Contado)</label>
        <input type="text" name="U_H8_NIF" value="{{ old('U_H8_NIF', $parte['U_H8_NIF'] ?? '') }}"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Asunto</label>
        <input type="text" name="Subject"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1"
            value="{{ old('Subject', $parte['Subject'] ?? '') }}">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Número de serie Eurowin</label>
        <input id="itemFamilyCode" type="text" name="U_H8_SerieEurowin"
            value="{{ old('U_H8_SerieEurowin', $parte['U_H8_SerieEurowin'] ?? '') }}" {{-- producto --}}
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">R.M.A</label>
        <input type="text" name="U_H8_RMA"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1"
            value="{{ old('U_H8_RMA', $parte['U_H8_RMA'] ?? '') }}">
    </div>
    <div class="col-span-2 grid grid-cols-2 {{ !empty($cliente['CardCode']) ? '' : 'hidden' }}">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Tipo de llamada</label>
        <select name="U_H8_MOTIVO" id="U_H8_MOTIVO"
            class=" block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <option value="" selected>Selecciona un motivo</option>
            <option value="ST" {{ old('U_H8_MOTIVO', $parte['U_H8_MOTIVO'] ?? '') == 'ST' ? 'selected' : '' }}>Servicio
                técnico</option>
            <option value="DEV" {{ old('U_H8_MOTIVO', $parte['U_H8_MOTIVO'] ?? '') == 'DEV' ? 'selected' : '' }}>Proveedor
            </option>
        </select>
    </div>
</div>