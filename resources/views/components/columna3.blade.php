<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">ID llamada</label>
            <input type="text" name="callID" class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                value="{{ $parte->callID ?? '' }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Num de documento</label>
            <input type="text" name="DocNum" class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                value="{{ $parte->DocNum ?? '' }}">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="Status" class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <option value="-3" {{ $parte->status ?? '' == "-3" ? 'selected' : '' }}>Abierto</option>
                <option value="-2" {{ $parte->status ?? '' == "-2" ? 'selected' : '' }}>Cerrado</option>
                <option value="-1" {{ $parte->status ?? '' == "-1" ? 'selected' : '' }}>En proceso</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Prioridad</label>
            <select name="Priority" class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <option value="H" {{ $parte->priority ?? '' == 'H' ? 'selected' : '' }}>Alta</option>
                <option value="M" {{ $parte->priority ?? '' == 'M' ? 'selected' : '' }}>Media</option>
                <option value="L" {{$parte->priority ?? '' == 'L' ? 'selected' : '' }}>Baja</option>
            </select>
        </div>
    </div>


    <!-- Botón alineado a la derecha justo después de las fechas -->
    <div class="flex justify-end">
        <button type="submit" id="buscarCliente" class="mb-4 bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6"
            onclick="this.disabled=true; this.innerText='Guardando...'; this.form.submit();">
            Guardar
        </button>
    </div>
</div>