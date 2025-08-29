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
@if (session('success'))
    <div id="alertaSuccess"
        class="max-w-xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-md"
        role="alert">
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('alertaSuccess').style.display='none';" type="button"
            class="ml-4 text-green-700 hover:text-green-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div id="alertaError"
        class="max-w-xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-md"
        role="alert">
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('alertaError').style.display='none';" type="button"
            class="ml-4 text-green-700 hover:text-green-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif
@if ($errors->any())
    <div id="alertaErrores"
        class="max-w-xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-start justify-between shadow-mdr"
        role="alert">
        <div>
            <strong>¡Ups! Ocurrió un error: </strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="document.getElementById('alertaErrores').style.display='none';" type="button"
            class="ml-4 text-green-700 hover:text-green-900 focus:outline-none">
            <span class="text-2xl">&times;</span>
        </button>
    </div>
@endif
<p class="text-center text-slate-800 font-semibold text-xl mb-6 tracking-wide flex justify-center items-center">
    {{ Auth::user()->username }}
</p>
<p class="text-center text-slate-800 font-semibold text-xl mb-6 tracking-wide flex justify-center items-center">
    Buscador de parte</p>

<form method="GET" action="{{ route('clientes.buscar')}}" class="flex justify-center items-center space-x-4 bg">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-8">
        @include('components.columna1')
        @include('components.columna2')
        @include('components.columna3')
    </div>
</form>