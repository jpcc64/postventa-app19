<div class="space-y-3 grid grid-cols-2 gap-2 p-2 rounded-md">
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nombre</label>
        <input type="text" name="U_H8_Nombre" value="{{ $parte->U_H8_Nombre ?? '' }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Nºteléfono</label>
        <input type="text" name="U_H8_Telefono" value="{{ $parte->U_H8_Telefono ?? '' }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">NIF</label>
        <input type="text" name="U_H8_NIF" value="{{ $parte->U_H8_NIF ?? '' }}"
            class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">Asunto</label>
        <input type="text" name="Subject" class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            value="{{ $parte->subject ?? '' }}">
    </div>
    <div class="col-span-2 grid grid-cols-2">
        <label class="block text-sm font-medium text-gray-700 col-span-2">R.M.A</label>
        <input type="text" name="U_H8_RMA" class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            value="{{ $parte->U_H8_RMA ?? '' }}">
    </div>
</div>