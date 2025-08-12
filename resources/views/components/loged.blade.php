<h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Notificación de Pedidos</h1>

<div class="flex justify-center items-center space-x-4">
    <form method="POST" action="{{ route('logout') }}" class="flex justify-center items-center">
        @csrf
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mb-4 text-center">
            Logout
        </button>
    </form>
    <a href="{{ route('parte') }}">
        <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded mb-4 text-center">
            Crear llamada de servicio
        </button>
    </a>
</div>
<p class="text-center text-slate-800 font-semibold text-xl mb-6 tracking-wide flex justify-center items-center">
    Bienvenido, {{ Auth::user()->username }}
</p>



<form method="GET" action="{{ route('clientes.buscar')}}" class="flex justify-center items-center space-x-4">
    <input type="text" id="search" name="id" placeholder="Número de parte"
        class="mb-4 p-3 border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none transition duration-300">
    <button class="mb-4 bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 px-6" type="submit">
        Buscar
    </button>
</form>
<div class="overflow-x-auto text-center bg-slate-50rounded-xl">
    @if(session('error'))
        <div class="mx-auto w-fit bg-red-100 text-red-700 border border-red-400 px-4 py-2 rounded mb-4 text-sm shadow">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div
            class="mx-auto w-fit bg-green-100 text-green-700 border border-green-400 px-4 py-2 rounded mb-4 text-sm shadow">
            {{ session('success') }}
        </div>
    @endif
    @if (!empty($clientes))
        <div class="shadow-md">
            <table class="min-w-full bg-white rounded-2xl overflow-hidden shadow-lg">
                <thead class="bg-slate-700 text-white">
                    <tr>
                        <th
                            class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider first:rounded-tl-2xl last:rounded-tr-2xl">
                            ID
                        </th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider">Estado</th>

                        <th
                            class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider last:rounded-tr-2xl">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-100 transition duration-300 ease-in-out">

                        <td class="px-4 py-3 text-sm text-gray-900">{{ $clientes->DocNum }}</td>
                        @if($clientes->custmrName)
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $clientes->custmrName }}</td>
                        @else
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $clientes->U_H8_Nombre }}</td>
                        @endif
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $clientes->itemName }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $clientes->Telephone }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            @if($clientes->status == '-1')
                                <p class="bg-red-500 text-white px-2 py-1 rounded">Cerrado</p>
                            @elseif($clientes->status == '-3')
                                <p class="bg-green-500 text-white px-2 py-1 rounded">Abierto</p>
                            @elseif($clientes->status == '-2')
                                <p class="bg-yellow-500 text-white px-2 py-1 rounded">Pendiente</p>
                            @else
                                {{ $clientes->status }}
                            @endif
                        </td>
                        <td class="flex justify-around m-4">
                            <form action="{{ route('avisar', trim($clientes->DocNum)) }}" method="post">
                                @csrf
                                <button
                                    class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                    Pedido listo
                                </button>
                            </form>
                            <a href="{{ route('clientes.show', trim($clientes->DocNum)) }}"
                                class="bg-rose-600 text-white px-5 py-2.5 rounded-lg hover:bg-rose-700 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-rose-400">
                                Ver cliente
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>