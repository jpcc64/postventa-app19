<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Arial&display=swap" rel="stylesheet">
    <style>
        /* Estilos personalizados basados en la imagen y adaptados a A4 */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background-color: #f0f0f0;
            /* Fondo para resaltar el contenedor principal */
        }

        .page {
            background: white;
            display: block;
            margin: 2rem auto;
            width: 21cm;
            min-height: 29.7cm;
            /* Usar min-height para flexibilidad */
            padding: 1.5rem;
            box-sizing: border-box;
        }

        .header-box {
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            padding: 0.5rem;
        }

        .content-box {
            border: 2px solid #000;
            border-radius: 2rem;
            padding: 1rem; /* Reducido de 1.5rem */
            margin-top: 1.5rem;
        }

        .content-box-section {
            min-height: 250px;
            border: 2px solid #000;
            border-radius: 2rem;
            margin-top: 1rem;
            overflow: hidden; /* Mantiene los bordes redondeados con el contenido interior */
        }

        .content-box-small {
            border: 2px solid #000;
            border-radius: 2rem;
            padding: 0rem 1.5rem; /* Reducido de 1rem 1.5rem */
            margin-top: 1.5rem;
        }

        .title-bar {
            background-color: #757575ff;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
        }

        .content-area {
            padding: 0rem 1.5rem; /* Reducido de 1rem 1.5rem */
            margin-left: 5px;
            white-space: pre-wrap;
        }

        /* Estilos de impresión */
        @media print {
            body,
            .page {
                margin: 0;
                box-shadow: none;
                background: white;
            }

            .title-bar {
                background-color: #a9a9a9 !important;
                -webkit-print-color-adjust: exact; /* Fuerza la impresión del color de fondo */
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
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

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print();">Imprimir</button>

    <div class="page">
        <header class="grid grid-cols-3 gap-1">
            <div class="header-box">
                <img src="{{ asset('storage/Logo_elec_euro_R.png') }}" alt="Logo" class="max-h-full">
            </div>
            <div class="header-box">
                <div class="text-left text-xs">
                    @if($parte['Origin'] == '3')
                    <p class="font-bold">COMERCIANTE MINORISTA</p>
                    <p>C/ Océano Atlántico, 35500 Arrecife, Las Palmas</p>
                    <p>928-47 34 34</p>
                    <p>C.I.F. A35421395</p>
                    @else
                    <p class="font-bold">COMERCIANTE MINORISTA</p>
                    <p>C/ Henequen, 43</p>
                    <p>928 85 01 40</p>
                    <p>C.I.F. A35421395</p>
                    @endif
                </div>
            </div>
            <div class="header-box">
                <img src="{{ asset('storage/QR_new.png') }}" alt="QR Code" class="h-16 w-16">
            </div>
        </header>

        <section class="grid grid-cols-3 gap-4">
            <div class="content-box-small col-span-1">
                <p>
                    <strong>PARTE S.A.T: </strong>
                    {{ $parte['DocNum'] ?? '' }}
                </p>
                <p>
                    <strong>FECHA </strong>
                    {{ isset($parte['CreationDate']) ? \Carbon\Carbon::parse($parte['CreationDate'])->format('d/m/Y') : '' }}
                </p>
                <p>
                    <strong>CLIENTE: </strong>
                    {{ $cliente['CardCode'] ?? '' }}
                </p>
                <p>
                    <strong>N.I.F. : </strong>
                    {{ $parte['U_H8_NIF'] ?? $cliente['FederalTaxID'] ?? '' }}
                </p>
            </div>

            <div class="content-box-small col-span-2">
                <p class="font-bold">CLIENTE</p>
                <p>{{ $cliente['CardCode'] ?? '' }} - {{ $parte['U_H8_Nombre'] ?? $cliente['CardName']  }}</p>
                <p>{{ $parte['BPBillToAddress'] ?? '' }}</p>
                <p>{{ $parte['BPShipToAddress'] ?? '' }}</p>
                <p><strong>TELÉFONO: </strong> {{ $parte['U_H8_Telefono'] ?? $parte['Telephone']   }}</p>
            </div>
        </section>

        <section class="content-box grid grid-cols-2">
            <div>
                <p><strong>Operario: </strong>
                    @isset($tecnico)
                    {{ $tecnico['FirstName'] ?? ''}}
                    @else
                    @endisset
                </p>
                <p><strong>Artículo: {{ $parte['ItemCode'] }}</strong> -- {{ $parte['ItemDescription'] ?? '' }}</p>
                <p><strong>Fecha de cierre: </strong>
                    {{ isset($parte['EndDueDate']) ? \Carbon\Carbon::parse($parte['EndDueDate'])->format('d/m/Y') : '' }}
                </p>
            </div>
            <div>
                <p><strong>Estado:</strong>
                    @switch($parte['Status'] ?? '')
                    @case('-3')
                    <span>Abierto</span>
                    @break
                    @case('-2')
                    <span>Pendiente</span>
                    @break
                    @case('-1')
                    <span>Cerrado</span>
                    @break
                    @default
                    <span></span>
                    @endswitch
                </p>
                <p><strong>R.M.A: </strong> {{ $parte['U_H8_RMA'] ?? '' }}</p>
                <p><strong>Núm. serie: </strong> {{ $parte['U_H8_SerieEurowin'] ?? '' }}</p>
                <p><strong>Origen: </strong>
                    @foreach($origen as $item)
                    @if($parte['Origin'] == $item['OriginID'])
                    {{ $item['Name'] }}
                    @endif
                    @endforeach
                </p>
            </div>
        </section>

        <section class="content-box-section">
            <div class="title-bar">
                <p>PROBLEMA DEL ARTÍCULO</p>
            </div>
            <div class="content-area overflow-hidden">
                {{ $parte['Description'] ?? '' }}
            </div>
        </section>

        <section class="content-box-section">
            <div class="title-bar">
                <p>SOLUCIÓN</p>
            </div>
            <div class="content-area overflow-hidden">
                {{$parte['Resolution'] ?? ''}}
            </div>
        </section>

        <footer>
            <div class="mt-4 text-xs space-y-2">
                <p class="mx-5">Durante la reparación el equipo puede perder parte o todos los datos que contenga, si hay información que no desea perder, guárdela por favor antes de entregar su equipo para reparación.</p>
                <p class="mx-5">Para cualquier consulta sobre el parte puede hacerlo a través del correo {{ $parte['Origin'] == '3' ? 'posventa_lanzarote@tiendaselectron.com' : 'posventa@tiendaselectron.com'}} especificando el número de parte.</p>
                <p class="mx-5">Transcurridos 6 meses tras la llamada de finalización de la reparación su producto pasará a reciclado.</p>
            </div>
            <div class="grid grid-cols-2 gap-4 m-2 text-center">
                <div class="font-bold">CONFORME CLIENTE</div>
                <div class="font-bold">FECHA ENTREGA</div>
            </div>
        </footer>
    </div>

</body>
</html>
