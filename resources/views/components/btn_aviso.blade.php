<!-- Button to open modal -->
 @if(isset($parte))
<button
    onclick="document.getElementById('confirmationModal_{{ $parte['ServiceCallID'] }}').classList.remove('hidden')"
    class="mb-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg py-3 px-6 mx-3 justify-right">
    Pedido listo
</button>

<!-- Modal -->
<div id="confirmationModal_{{ $parte['ServiceCallID'] }}"
    onclick="if(event.target == this) this.classList.add('hidden')"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 9999;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmar envío</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Se enviará un mensaje al siguiente número de teléfono:
                </p>
                <p class="text-lg font-semibold text-gray-800 my-2">
                    {{ $parte['Telephone'] ?? 'No disponible' }}
                </p>
                <p class="text-sm text-gray-500">
                    Si desea enviarlo a otro número, por favor, introdúzcalo a continuación.
                </p>
                <form id="avisarForm_{{ $parte['ServiceCallID'] }}"
                    action="{{ route('cliente.avisar', trim($parte['ServiceCallID'])) }}" method="POST" class="mt-4">
                    @csrf
                    <input type="text" name="telefono_alternativo" placeholder="Número de teléfono alternativo"
                        class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" />
                    <input type="hidden" name="telefono_original" value="{{ $parte['Telephone'] ?? '' }}">
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button
                    onclick="document.getElementById('confirmationModal_{{ $parte['ServiceCallID'] }}').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancelar
                </button>
                <button onclick="document.getElementById('avisarForm_{{ $parte['ServiceCallID'] }}').submit()"
                    class="px-4 py-2 bg-emerald-600 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    Enviar
                </button>
            </div>
        </div>
    </div>
</div>
@endif
<!-- End of Modal -->