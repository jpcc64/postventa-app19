<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Parte {{ $parte['ServiceCallID'] ?? '' }}</title>
    <!-- Inclusión de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Arial&display=swap" rel="stylesheet">
    <style>
        /* Estilos personalizados basados en la imagen y adaptados a A4 */
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background-color: #f0f0f0; /* Fondo para resaltar el contenedor principal */
        }
        .page {
            background: white;
            display: block;
            margin: 2rem auto;
            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
            width: 21cm;
            min-height: 29.7cm; /* Usar min-height para flexibilidad */
            padding: 1.5rem;
            box-sizing: border-box;
            border: 3px solid #000;
        }
        .header-box {
            border: 2px solid #000;
            height: 80px; /* Altura fija para los cuadros de la cabecera */
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            padding: 0.5rem;
        }
        .content-box {
            border: 2px solid #000;
            border-radius: 2rem; /* Bordes muy redondeados */
            padding: 1.5rem;
            margin-top: 1.5rem; /* Espacio entre divs */
            position: relative; /* Necesario para la barra gris */
            overflow: hidden; /* Para que la barra gris no se salga */
        }
        .content-box-small {
             border: 2px solid #000;
            border-radius: 2rem;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
        }
        .grey-bar {
            background-color: #a9a9a9; /* Gris */
            border-radius: 2rem 2rem 0 0; /* Redondeado solo arriba */
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40%; /* Ocupa una porción del contenedor */
            z-index: 1;
        }
        .content-over-bar {
            position: relative;
            z-index: 2; /* Asegura que el texto esté sobre la barra */
        }

        /* Estilos de impresión */
        @media print {
            body, .page {
                margin: 0;
                box-shadow: none;
                background: white;
            }
            .no-print {
                display: none;
            }
        }
        .print-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background: #007bff;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    
    <!-- Botón para imprimir que no se verá en el papel -->
    <button onclick="window.print()" class="print-button no-print">Imprimir</button>

    <div class="page">
        <!-- Cabecera: LOGO, TEXTO, QR -->
        <header class="grid grid-cols-3 gap-4">
            <div class="header-box">
                <!-- LOGO -->
                <img src="{{ asset('storage/app/public/Logo_elec_euro_R.png') }}" alt="Logo" class="max-h-full">
            </div>
            <div class="header-box">
                <!-- TEXTO -->
                <div class="text-center text-xs">
                    <p class="font-bold">COMERCIANTE MINORISTA</p>
                    <p>C/ Henequen, 43</p>
                    <p>C.I.F. A35421395</p>
                </div>
            </div>
            <div class="header-box">
                <!-- QR -->
                <img src="{{ asset('storage/app/public/QR_new.png') }}" alt="QR Code" class="h-16 w-16">
            </div>
        </header>
        
        <!-- Contenido 1: Dos cajas pequeñas -->
        <section class="grid grid-cols-3 gap-4">
             <div class="content-box-small col-span-1">
                <p class="font-bold">FECHA</p>
                <p>{{ $parte['CreationDate'] ?? 'N/A' }}</p>
            </div>
            <div class="content-box-small col-span-2">
                <p class="font-bold">CLIENTE</p>
                <p>{{ $cliente['CardCode'] ?? 'N/A' }} - {{ $cliente['CardName'] ?? 'N/A' }}</p>
            </div>
        </section>

        <!-- Contenido 2: Caja larga -->
        <section class="content-box">
             <p class="font-bold">DATOS DE CONTACTO Y PARTE</p>
             <p><strong>NIF:</strong> {{ $cliente['FederalTaxID'] ?? 'N/A' }}</p>
             <p><strong>Dirección:</strong> {{ $parte['BPBillToAddress'] ?? 'N/A' }}</p>
             <p><strong>Teléfono:</strong> {{ $cliente['Phone1'] ?? 'N/A' }}</p>
             <p><strong>Parte S.A.T:</strong> {{ $parte['ServiceCallID'] ?? 'N/A' }}</p>
        </section>

        <!-- Contenido 3: Caja con barra gris -->
        <section class="content-box">
            <div class="grey-bar"></div>
            <div class="content-over-bar">
                <p class="font-bold">PROBLEMA DEL ARTÍCULO</p>
                <p><strong>Artículo:</strong> {{-- Aquí iría la variable del artículo --}}</p>
                <p><strong>Descripción:</strong> {{-- Aquí iría la variable de la descripción del problema --}}</p>
            </div>
        </section>

        <!-- Contenido 4: Caja con barra gris -->
        <section class="content-box">
            <div class="grey-bar"></div>
            <div class="content-over-bar">
                <p class="font-bold">SOLUCIÓN</p>
                <p>{{-- Aquí iría la variable de la solución --}}</p>
            </div>
        </section>

    </div>

</body>
</html>
