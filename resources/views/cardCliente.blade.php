@include('components.head')



<div class="w-full flex justify-center mb-6">
    <div class="w-80 bg-white rounded-lg shadow-lg p-6 text-center border border-gray-200">
        <p class="text-gray-700"><span class="font-semibold">ID:</span> {{ $cliente->customer }}</p>
        @if($cliente->custmrName)
            <p class="text-gray-700"><span class="font-semibold">Nombre:</span> {{ $cliente->custmrName }}</p>
        @else
            <p class="text-gray-700"><span class="font-semibold">Nombre:</span> {{ $cliente->U_H_Nombre }}</p>
        @endif
        <p class="text-gray-700"><span class="font-semibold">Teléfono:</span> {{ $cliente->Telephone }}</p>
        <p class="text-gray-700"><span class="font-semibold">ID producto:</span> {{ $cliente->itemCode }}</p>
        <p class="text-gray-700"><span class="font-semibold">Nombre Producto:</span> {{ $cliente->itemName }}</p>
        <form class="inline-block" action="{{ route('avisar', trim($cliente->DocNum)) }}" method="post">
            @csrf
            <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-800">
                Avisar
            </button>
        </form>
        <a href="{{ route('home') }}"
            class="inline-block bg-blue-600 text-white px-2 py-1 mb-4 rounded hover:bg-blue-700"> Atrás ↩️</a>
    </div>
</div>

@include('components.footer')