<div x-data="{ tab: 'general' }" class="rounded grid  mb-6 w-50">

    <div class="w-full col-span-3">
        <div class="mb-6 ">
            <div class="flex border-b mb-4">
                <button type="button" @click="tab = 'general'"
                    :class="tab === 'general' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                    class="px-4 py-2">
                    General
                </button>
                <button type="button" @click="tab = 'comentario'"
                    :class="tab === 'comentario' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                    class="px-4 py-2">
                    Comentario
                </button>
                <button type="button" @click="tab = 'resolucion'"
                    :class="tab === 'resolucion' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                    class="px-4 py-2">
                    Resolución
                </button>

            </div>
            <div x-show="tab === 'general'" x-cloak x-transition class="space-y-4 mb-6 grid grid-cols-3 gap-4">
                <div class="mt-4">
                    <label for="origin-select" class="block text-sm font-medium">Origen</label>
                    <select name="Origin" id="origin-select"
                        class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">-- Sin asignar --</option>
                        @foreach($origenes as $origen)
                            <option value="{{ $origen['OriginID'] }}" {{ isset($parte['Origin']) && $parte['Origin'] == $origen['OriginID'] ? 'selected' : '' }}>
                                {{ $origen['Name'] }}
                            </option>
                        @endforeach

                    </select>
                </div>
                <div class="mt-4 relative">
                    <label class="block text-sm font-medium">Técnico</label>
                    <input type="text" hidden name="TechnicianCode" id="techCode"
                        value="{{ old('TechnicianCode', $parte['TechnicianCode'] ?? '') }}">
                    <input type="text" name="TechnicianName" id="techName" value="{{ $tecnico->FirstName ?? ''}}"
                        class="mt-1 block w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <div id="sugerenciasTecnico"
                        class="absolute left-0 top-full w-full bg-white shadow-md rounded-md max-h-60 overflow-y-auto mt-1 z-10">
                    </div>
                </div>

            </div>

            <div x-show="tab === 'resolucion'" x-cloak x-transition class="space-y-4 mb-6 col-3">
                <label class="block text-sm font-medium">Observaciones</label>
                <textarea name="Resolution" rows="3"
                    class="mt-1 block w-2/4 rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('Resolution', $parte['Resolution'] ?? '') }}</textarea>
            </div>

            <div x-show="tab === 'comentario'" x-cloak x-transition class="space-y-4 mb-6">
                <label class="block text-sm font-medium">Comentario</label>
                <textarea name="Description" rows="3"
                    class="mt-1 block w-2/4 rounded-md border border-gray-400 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('Description', $parte['Description'] ?? '') }}</textarea>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            let tecnicoTimeout = null;

            $('#techName').on('input', function () {
                let query = $(this).val();

                clearTimeout(tecnicoTimeout); // Limpiar el timeout anterior

                tecnicoTimeout = setTimeout(function () {
                    if (query.length >= 1) {
                        $.ajax({
                            url: '{{ route("tecnico.sugerencias") }}',
                            type: 'GET',
                            data: { term: query },
                            success: function (data) {
                                let sugerencias = $('#sugerenciasTecnico');
                                sugerencias.empty();

                                if (Array.isArray(data) && data.length > 0) {
                                    let lista = $('<ul class="max-h-60 overflow-y-auto"></ul>');
                                    data.forEach(function (tecnico) {
                                        lista.append(`
                                        <li class="sugerencia cursor-pointer px-4 py-2 hover:bg-blue-100 transition-all border-b border-gray-200">
                                            <p class="text-xs text-gray-500" data-id="${tecnico.EmployeeID}">${tecnico.EmployeeID}</p>
                                            <p class="font-semibold text-sm text-gray-800" data-id="${tecnico.FirstName}">${tecnico.FirstName}</p>
                                        </li>
                                    `);
                                    });
                                    sugerencias.append(lista);

                                    $('.sugerencia').on('click', function (e) {
                                        e.preventDefault();
                                        let EmployeeID = $(this).find('p').eq(0).text();
                                        let EmployeeName = $(this).find('p').eq(1).text();
                                        $('#techCode').val(EmployeeID);
                                        $('#techName').val(EmployeeName);
                                        $('#sugerenciasTecnico').empty();
                                    });
                                } else {
                                    sugerencias.append('<div class="px-4 py-2 text-gray-500">No se encontraron técnicos.</div>');
                                }
                            }
                        });
                    } else {
                        $('#sugerenciasTecnico').empty();
                    }
                }, 300);
            });

            // Cerrar modal al hacer click fuera
            $(document).on('click', function (e) {
                if ($(e.target).is('#sugerenciasTecnico')) {
                    $('#sugerenciasTecnico').addClass('hidden');
                }
            });
        });
    </script>