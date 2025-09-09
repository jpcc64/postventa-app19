<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Num de parte</label>

            <input type="text" name="DocNum" {{ !empty($parte['DocNum']) ? 'readonly' : '' }}
                class="mt-1 block w-full rounded-md border border-gray-400 {{ !empty($parte['DocNum']) ? 'bg-gray-300' : '' }} shadow-sm p-1"
                value="{{ $parte['DocNum'] ?? '' }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">ID llamada</label>
            <input type="text" name="ServiceCallID" {{ !empty($parte['DocNum']) ? 'readonly' : '' }}
                class="mt-1 block w-full rounded-md border border-gray-400 {{ !empty($parte['DocNum']) ? 'bg-gray-300' : '' }}  shadow-sm p-1"
                value="{{ $parte['ServiceCallID'] ?? '' }}">
        </div>
    </div>


    <div class="grid grid-cols-2 gap-4">
        <div>
            <div>
                <label class="block text-sm font-medium text-gray-700 col-span-2">Codigo de cliente</label>
                <input type="text" name="CustomerCode"
                    value="{{ old('CardCode', $cliente['Cardcode'] ?? ($parte['CustomerCode'] ?? '')) }}"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-1">
            </div>

        </div>
      
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="Status"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Seleccione un estado</option>
                    <option value="-3" {{ isset($parte['Status']) && $parte['Status'] == -3 ? 'selected' : '' }}>
                        Abierto
                    </option>
                    <option value="-2" {{ isset($parte['Status']) && $parte['Status'] == -2 ? 'selected' : '' }}>
                        En proceso
                    </option>
                    <option value="-1" {{ isset($parte['Status']) && $parte['Status'] == -1 ? 'selected' : '' }}>
                        Cerrado
                    </option>
                </select>
                <!-- <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                                        <select name="Priority"
                                            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="scp_High" {{ isset($parte['Priority']) && $parte['Priority'] == 'scp_High' ? 'selected' : '' }}>Alta</option>
                                            <option value="scp_Medium" {{ isset($parte['Priority']) && $parte['Priority'] == 'scp_Medium' ? 'selected' : '' }}>Media</option>
                                            <option value="scp_Low" {{ isset($parte['Priority']) && $parte['Priority'] == 'scp_Low' ? 'selected' : '' }}>Baja</option>
                                        </select> -->
            </div>
            <div>
                <label for="CreationDate" class="block text-sm font-medium text-gray-700">Fecha de creación</label>
                <input type="date" name="CreationDate"{{ isset($parte) ? 'disabled' : '' }}  id="CreationDate"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    value="{{ $parte['CreationDate'] ?? '' }}">
            </div>
            <div>
                @if(isset($parte) && $parte['Status'] == -1)
                    
                <label for="ResolutionOnDate" class="block text-sm font-medium text-gray-700">Fecha de
                    resolución</label>
                <input type="date" name="ResolutionOnDate" disabled id="ResolutionOnDate"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    value="{{ $parte['ResolutionOnDate'] ?? '' }}">
                    @endif
            </div>
    </div>



    <div class="flex justify-end">

        <button type="submit" id="buscarCliente"
            class="mb-4 bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6 mx-3"
            onclick="this.disabled=true; this.innerText='Guardando...'; this.form.submit();">
            Guardar
        </button>
        <button type="button" id="limpiarFormulario"
            class="mb-4 bg-rose-600 hover:bg-rose-700 text-white rounded-lg py-3 px-6 mx-3"
            onclick="window.location.href='{{ route('parte') }}';">
            Cancelar
        </button>
    </div>
</div>
