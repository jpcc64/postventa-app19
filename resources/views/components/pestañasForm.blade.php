<div class="w-full col-span-3">
    <div class="mb-6 ">
        <div class="flex border-b mb-4">
            <button type="button" @click="tab = 'interlocutor'"
                :class="tab === 'interlocutor' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                class="px-4 py-2">
                Interlocutor
            </button>
            <button type="button" @click="tab = 'general'"
                :class="tab === 'general' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                class="px-4 py-2">
                General
            </button>
            <button type="button" @click="tab = 'resolucion'"
                :class="tab === 'resolucion' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                class="px-4 py-2">
                Resolución
            </button>
            <button type="button" @click="tab = 'comentario'"
                :class="tab === 'comentario' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                class="px-4 py-2">
                Comentario
            </button>
        </div>

        <div x-show="tab === 'interlocutor'" x-cloak x-transition class="space-y-4 mb-6 grid grid-cols-4 gap-4">
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Telefono</label>
                <input type="text" name="BPPhone2"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPPhone1'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="text" name="BPeMail"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPE_Mail'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Codigo Postal</label>
                <input type="text" name="BPShipAddr"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50"
                    value="{{ $parte['ServiceCallBPAddressComponents']['ShipToZipCode'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium">Enviar a</label>
                <select name="BPShipToCode"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="" {{ old('BPShipToCode', $parte['BPShipCode'] ?? '') == '' ? 'selected' : '' }}>
                        Seleccione una dirección</option>
                    <option value="DIRECCIÓN 1" {{ old('BPShipToCode', $parte['BPShipCode'] ?? '') == 'DIRECCIÓN 1' ? 'selected' : '' }}>DIRECCIÓN 1</option>
                    <option value="DIRECCIÓN 2" {{ old('BPShipToCode', $parte['BPShipCode'] ?? '') == 'DIRECCIÓN 2' ? 'selected' : '' }}>DIRECCIÓN 2</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Destinatario de la factura</label>
                <input type="text" name="BPBillCode"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    value="{{ $parte['BPBillCode'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium">Dirección de la factura</label>
                <input type="text" name="BPBillAddr"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPBillAddr'] ?? '' }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Teléfono Móvil</label>
                <input type="text" name="BPCellular"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPCellular'] ?? '' }}">
            </div>
            <div class="col-span-4">
                <h4 class="text-md font-semibold mt-4 mb-2 underline">Dirección de Envío</h4>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Calle Envío</label>
                <input type="text" name="BPShipToStreet"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPShipToStreet'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Ciudad Envío</label>
                <input type="text" name="BPShipToCity"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPShipToCity'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Código Postal Envío</label>
                <input type="text" name="BPShipToZipCode"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPShipToZipCode'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Provincia Envío</label>
                <input type="text" name="BPShipToCounty"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPShipToCounty'] ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tipo Dirección Envío</label>
                <input type="text" name="BPShipToAddress"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['BPShipToAddress'] ?? 'B' }}"> {{-- Valor
                por defecto si no existe --}}
            </div>


        </div>

        <div x-show="tab === 'general'" x-cloak x-transition class="space-y-4 mb-6 grid grid-cols-3 gap-4">
            <div class="mt-4">
                <label class="block text-sm font-medium">Origen</label>
                <input type="text" name="Origin"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('Origin', $parte['Origin'] ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-medium">Técnico</label>
                <input type="text" name="TechnicianCode"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50"
                    value="{{ old('TechnicianCode', $parte['TechnicianCode'] ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-medium">Motivo</label>
                <input type="text" name="U_H8_MOTIVO"
                    class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    focus:ring-blue-200 focus:ring-opacity-50" value="{{ $parte['U_H8_MOTIVO'] ?? '' }}">
            </div>
        </div>

        <div x-show="tab === 'resolucion'" x-cloak x-transition class="space-y-4 mb-6 col-3">
            <label class="block text-sm font-medium">Observaciones</label>
            <textarea name="Resolution" rows="3"
                class="mt-1 block w-2/4 rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                ull rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring
                focus:ring-blue-200 focus:ring-opacity-50">{{ old('Resolution', $parte['Resolution'] ?? '') }}</textarea>
        </div>

        <div x-show="tab === 'comentario'" x-cloak x-transition class="space-y-4 mb-6">
            <label class="block text-sm font-medium">Comentario</label>
            <textarea name="Description" rows="3"
                class="mt-1 block w-2/4 rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                ull rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring
                focus:ring-blue-200 focus:ring-opacity-50">{{ old('Description', $parte['Description'] ?? '') }}</textarea>
        </div>
    </div>
</div>