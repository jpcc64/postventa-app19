<link rel="stylesheet" href="{{ asset('css/anexos-styles.css') }}">

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
                <!-- <button type="button" @click="tab = 'anexo'"
                    :class="tab === 'anexo' ? 'border-b-2 border-blue-600 text-blue-800' : 'text-gray-800'"
                    class="px-4 py-2">
                    Anexos
                </button> -->

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
                    <input type="text" name="TechnicianName" id="techName"
                        value="{{ old('TechnicianName', $tecnico['FirstName'] ?? '') }}"
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
            <!-- <div x-show="tab === 'anexo'" x-cloak x-transition class="space-y-4 mb-6">
                <div id="drop-zone"

                ///////////////  ///////////////
  /////////////// Atachments2 tabla para los anexos  ///////////////
                ///////////////  ///////////////

                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                        </svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o
                            arrastra y suelta
                        </p>
                        <p class="text-xs text-gray-500">Imágenes, PDF, DOCX (MAX. 5MB por archivo)</p>
                    </div>
                    <input id="file-input" type="file" class="hidden" multiple />
                </div>

                <div id="preview-container" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                   
                </div>
            </div> -->


            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <script>
                
                    document.addEventListener('DOMContentLoaded', function () {
                        // --- LÓGICA DE ANEXOS (DRAG & DROP) ---
                        const dropZone = document.getElementById('drop-zone');
                        const fileInput = document.getElementById('file-input');
                        const previewContainer = document.getElementById('preview-container');

                        if (dropZone) {
                            const dataTransfer = new DataTransfer();

                            dropZone.addEventListener('click', () => fileInput.click());

                            dropZone.addEventListener('dragover', (e) => {
                                e.preventDefault();
                                dropZone.classList.add('drag-over');
                            });

                            dropZone.addEventListener('dragleave', () => {
                                dropZone.classList.remove('drag-over');
                            });

                            dropZone.addEventListener('drop', (e) => {
                                e.preventDefault();
                                dropZone.classList.remove('drag-over');
                                handleFiles(e.dataTransfer.files);
                            });

                            fileInput.addEventListener('change', () => {
                                handleFiles(fileInput.files);
                            });

                            function handleFiles(files) {
                                for (const file of files) {
                                    const alreadyExists = Array.from(dataTransfer.files).some(f => f.name === file.name && f.size === file.size);
                                    if (!alreadyExists) {
                                        dataTransfer.items.add(file);
                                        createPreview(file);
                                    }
                                }
                                fileInput.files = dataTransfer.files;
                            }

                            function createPreview(file) {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    const previewItem = document.createElement('div');
                                    previewItem.className = 'preview-item';
                                    let filePreview;
                                    if (file.type.startsWith('image/')) {
                                        filePreview = `<img src="${e.target.result}" alt="${file.name}">`;
                                    } else {
                                        const icon = file.type === 'application/pdf' ?
                                            `<svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>` :
                                            `<svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;
                                        filePreview = `<div class="file-icon">${icon}</div>`;
                                    }
                                    previewItem.innerHTML = `
                    ${filePreview}
                    <div class="file-info"><p class="file-name">${file.name}</p></div>
                    <button type="button" class="remove-file-btn" title="Eliminar archivo">&times;</button>
                `;
                                    previewContainer.appendChild(previewItem);
                                    previewItem.querySelector('.remove-file-btn').addEventListener('click', () => {
                                        const newFiles = new DataTransfer();
                                        Array.from(dataTransfer.files)
                                            .filter(f => f.name !== file.name || f.size !== file.size)
                                            .forEach(f => newFiles.items.add(f));

                                        dataTransfer.items.clear();
                                        Array.from(newFiles.files).forEach(f => dataTransfer.items.add(f));

                                        fileInput.files = dataTransfer.files;
                                        previewItem.remove();
                                    });
                                };
                                reader.readAsDataURL(file);
                            }
                        }
                    });
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