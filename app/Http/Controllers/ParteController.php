<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Auth;
use App\Events\AccionUsuarioRegistrada; 
class ParteController extends Controller
{
    private $productoController;

    public function __construct(ProductoController $productoController)
    {
        $this->productoController = $productoController;
    }

    // ... (otros métodos como index, buscar, etc. permanecen igual) ...
    public function index()
    {
        return view('parteFormulario', ['origenes' => $this->consultarOrigen()]);
    }

    public function buscar(Request $request)
    {
        AccionUsuarioRegistrada::dispatch(Auth::user(), 'Búsqueda de parte', ['termino' => $request->input('buscar')]);
        $id = $request->input('buscar');
        if ($id == null) {
            return back()->with('error', 'No se buscó ningún cliente.');
        }
        $busqueda = strtoupper(trim($id));
        $clientes = $this->consultarClientes($busqueda);

        if (empty($clientes)) {
            return back()->with('error', 'No se encontró ningún cliente.');
        }
        $partes = $this->consultarPartes($clientes[0]['CardCode'], 'CustomerCode');

        $tecnico = null;
        if (!empty($partes) && isset($partes[0]['TechnicianCode'])) {
            $tecnico = $this->nombreTecnico($partes[0]['TechnicianCode']);
        }

        if (count($partes) >= 2) {
            return view('parteFormulario', [
                'cliente' => $clientes[0],
                'partes' => $partes,
                'origenes' => $this->consultarOrigen(),
                'tecnico' => $tecnico
            ])->with('success', 'Se encontraron varios partes. Por favor, selecciona uno.');
        }

        return view('parteFormulario', [
            'cliente' => $clientes[0],
            'parte' => $partes[0] ?? null,
            'origenes' => $this->consultarOrigen(),
            'tecnico' => $tecnico
        ])->with('success', 'Parte encontrado para el cliente.');
    }

    public function buscarRMA(Request $request)
    {
        AccionUsuarioRegistrada::dispatch(Auth::user(), 'Búsqueda de RMA', ['termino' => $request->input('busquedaRMA')]);
        $id = $request->input('busquedaRMA');
        if ($id == null) {
            return back()->with('error', 'No se encontró ningún RMA.');
        }
        $busqueda = strtoupper(trim($id));
        $rmas = $this->consultarPartes($busqueda, 'U_H8_RMA');

        if (empty($rmas)) {
            return back()->with('error', 'No se encontró ningún RMA.');
        }

        $cliente = $this->consultarClientes($rmas[0]['CustomerCode'] ?? '');
        $tecnico = null;
        if (isset($rmas[0]['TechnicianCode'])) {
            $tecnico = $this->nombreTecnico($rmas[0]['TechnicianCode']);
        }

        return view('parteFormulario', [
            'parte' => $rmas[0],
            'cliente' => $cliente[0] ?? null,
            'origenes' => $this->consultarOrigen(),
            'tecnico' => $tecnico
        ])->with('success', 'Parte encontrado.');
    }

    public function nuevoParte($id)
    {
        $cliente = $this->consultarClientes($id);
        return view('parteFormulario', [
            'cliente' => $cliente[0] ?? null,
            'origenes' => $this->consultarOrigen(),
            'tecnico' => null
        ]);
    }

    public function sugerencias(Request $request)
    {
        $term = strtoupper(trim($request->get('term')));
        $busquedaConES = $term;
        if (!str_starts_with($term, 'ES')) {
            $busquedaConES = 'ES' . $term;
        }
        $term = str_replace("'", "''", $term);

        $accion = "consultar_BusinessPartners";
        $data = [
            "select" => "CardCode,CardName,Phone1,FederalTaxID",
            "where" => "substringof('$term', CardCode) or substringof('$term', CardName) or substringof('$term', Phone1) or FederalTaxID eq '$term' or FederalTaxID eq '$busquedaConES'"
        ];

        $response = Http::asForm()->post(env('API_SAP_URL'), [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => env('API_SAP_USER', 'dani'),
                'datos' => $data
            ])
        ]);

        $body = $response->json();
        return ($body['value'] ?? []);
    }

    public function consultarClientes($busqueda)
    {
        if (empty($busqueda))
            return [];

        $busquedaConES = $busqueda;
        if (!str_starts_with($busqueda, 'ES')) {
            $busquedaConES = 'ES' . $busqueda;
        }
        $busqueda = str_replace("'", "''", $busqueda);

        $accion = "consultar_BusinessPartners";
        $data = [
            "select" => "CardCode,CardName,Phone1,FederalTaxID",
            "where" => "substringof('$busqueda', CardCode) or substringof('$busqueda', CardName) or substringof('$busqueda', Phone1) or FederalTaxID eq '$busqueda' or FederalTaxID eq '$busquedaConES'"
        ];

        $response = Http::asForm()->post(env('API_SAP_URL'), [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => env('API_SAP_USER', 'dani'),
                'datos' => $data
            ])
        ]);

        $body = $response->json();
        return ($body['value'] ?? []);
    }

    public function consultarPartes($customer, $col)
    {
        if (empty($customer)) {
            return [];
        }

        $accion = "consultar_ServiceCalls";
        $whereClause = "";

        if ($col == 'ServiceCallID' || $col == 'DocNum') {
            $whereClause = "$col eq $customer";
        } else {
            $customer = str_replace("'", "''", $customer);
            $whereClause = "substringof('$customer', $col)";
        }

        $data = [
            "where" => $whereClause,
            "order" => "ServiceCallID",
        ];

        $response = Http::asForm()->post(env('API_SAP_URL'), [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => env('API_SAP_USER', 'dani'),
                'datos' => $data
            ])
        ]);

        $body = $response->json();
        return ($body['value'] ?? []);
    }

    public function showParte($callID)
    {
        $parte = $this->consultarPartes($callID, 'ServiceCallID');
        $parte = $parte[0] ?? null;

        if (!$parte) {
            return redirect()->route('parte.index')->with('error', 'Parte no encontrado.');
        }

        $cliente = $this->consultarClientes($parte['CustomerCode']);
        $tecnico = $this->nombreTecnico($parte['TechnicianCode'] ?? null);

        return view('parteFormulario', [
            'cliente' => $cliente[0] ?? null,
            'parte' => $parte,
            'origenes' => $this->consultarOrigen(),
            'tecnico' => $tecnico
        ]);
    }

    public function crear(Request $request)
    {
        $this->validarCamposSAP($request);
        $datosFormulario = $request->except('anexos'); // Datos del formulario principal

        $serviceCallID = $datosFormulario['ServiceCallID'] ?? null;
        $docNum = $datosFormulario['DocNum'] ?? null;

        $accionServiceCall = (empty($serviceCallID) && empty($docNum))
            ? 'crear_ServiceCalls'
            : 'modificar_ServiceCalls';

        try {
            $attachmentEntryID = null;
         // dd($request->all());
            if ($request->hasFile('anexos')) {
                // Guarda los archivos en el servidor y crea la entrada en Attachments2
                $attachmentEntryID = $this->crearEntradaDeAnexosSAP($request->file('anexos'));
                if ($attachmentEntryID) {
                    // Si se creó la entrada de anexos, la incluimos en los datos del Service Call
                    $datosFormulario['AttachmentEntry'] = $attachmentEntryID;
                    Log::info("Anexo(s) subido(s). El Service Call se actualizará con AttachmentEntry: {$attachmentEntryID}");
                } else {
                    Log::warning("No se pudieron crear las entradas de anexos en SAP Attachments2.");
                }
            }

            // --- PASO 2: CREAR O MODIFICAR EL PARTE (Service Call) ---
            Log::info('Paso 2: Enviando datos del parte a SAP (incluyendo AttachmentEntry si existe)', [
                'accion' => $accionServiceCall,
                'datos' => $datosFormulario
            ]);

            $responseParte = Http::asForm()->post(env('API_SAP_URL'), [
                'json' => json_encode([
                    'accion' => $accionServiceCall,
                    'usuario' => env('API_SAP_USER', 'dani'),
                    'datos' => $datosFormulario // ESTO ES CLAVE: $datosFormulario AHORA PUEDE CONTENER 'AttachmentEntry'
                ])
            ]);

            $bodyParte = $responseParte->json();
            if (!$responseParte->successful() || isset($bodyParte['error'])) {
                Log::error('Error de SAP al crear/modificar el parte', ['body' => $bodyParte]);
                return back()->withInput()->withErrors(['api_error' => 'Error de SAP: ' . $this->extraerMensajeErrorSAP($bodyParte)]);
            }

            // --- PASO 3: OBTENER EL ID FINAL DEL PARTE ---
            $finalServiceCallID = ($accionServiceCall == 'crear_ServiceCalls')
                ? ($bodyParte['ServiceCallID'] ?? $serviceCallID) // Si se creó, usa el nuevo ID. Si se modificó, usa el original.
                : $serviceCallID;

            if (!$finalServiceCallID) {
                return back()->withInput()->withErrors(['api_error' => 'No se pudo obtener el ID del parte después de la operación.']);
            }

            // --- DISPARAMOS EL EVENTO DE AUDITORÍA ---
            $usuario = Auth::user();
            if ($usuario) { // Ya no necesitamos $parte aquí, podemos usar $finalServiceCallID
                $mensajeAccion = ($accionServiceCall == 'crear_ServiceCalls' ? 'Creó' : 'Modificó') . " el parte #{$finalServiceCallID}";
                AccionUsuarioRegistrada::dispatch($usuario, $mensajeAccion, ['ServiceCallID' => $finalServiceCallID, 'AttachmentEntry' => $attachmentEntryID]);
            }
            // --- FIN DEL DISPARO DEL EVENTO ---

            // --- PASO 4: DEVOLVER LA VISTA CON LOS DATOS FRESCOS ---
            // Consulta el parte completo con el ID final para mostrarlo actualizado
            $parteCompleto = $this->consultarPartes($finalServiceCallID, 'ServiceCallID');
            $parte = $parteCompleto[0] ?? null;

            if (!$parte) {
                return back()->withInput()->withErrors(['api_error' => 'El parte se guardó, pero no se pudo recuperar para mostrarlo.']);
            }

            $cliente = $this->consultarClientes($parte['CustomerCode']);
            $tecnico = $this->nombreTecnico($parte['TechnicianCode'] ?? '');
            $successMessage = ($accionServiceCall == 'crear_ServiceCalls' ? 'Parte creado' : 'Parte modificado') . ' con éxito.';

            return view('parteFormulario', [
                'success' => $successMessage,
                'parte' => $parte,
                'cliente' => $cliente[0] ?? null,
                'origenes' => $this->consultarOrigen(),
                'tecnico' => $tecnico
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción al enviar a SAP', ['exception' => $e->getMessage(), 'request_data' => $request->all()]);
            return back()->withInput()->withErrors(['exception' => 'Excepción: ' . $e->getMessage()]);
        }
    }

    /**
     * Guarda los archivos en el servidor y crea una entrada 'Attachments2' en SAP.
     * Devuelve el AbsoluteEntry de la entrada de Attachments2 creada.
     */
    private function crearEntradaDeAnexosSAP(array $files): ?int
    {
        $attachmentPath = env('SAP_ATTACHMENT_PATH');
        if (!$attachmentPath) {
            Log::error("La ruta de anexos SAP_ATTACHMENT_PATH no está configurada en el archivo .env");
            return null;
        }

        $lines = [];
        foreach ($files as $file) {
            try {
                // 1. Guardar el archivo en la carpeta compartida
                // Se genera un nombre único para evitar colisiones
                $uniqueFileName = uniqid() . '_' . $file->getClientOriginalName();
                $file->move($attachmentPath, $uniqueFileName);

                // 2. Preparar la línea para la API de SAP Attachments2
                $lines[] = [
                    "FileName" => pathinfo($uniqueFileName, PATHINFO_FILENAME), // Nombre sin extensión
                    "FileExtension" => $file->getClientOriginalExtension(),
                    "SourcePath" => $attachmentPath, // Ruta donde SAP encontrará el archivo
                    "UserID" => Auth::id() ?? 1     // Asigna el UserID del usuario actual si existe, o un valor predeterminado
                ];
            } catch (\Exception $e) {
                Log::error("No se pudo guardar el anexo '{$file->getClientOriginalName()}' en el servidor.", ['exception' => $e->getMessage()]);
            }
        }

        if (empty($lines)) {
            Log::warning("No hay archivos válidos para enviar a Attachments2.");
            return null;
        }
        // 3. Llamar a la API de SAP para crear la entrada de Anexos
      //  dd($lines);
        try {
            $response = Http::asForm()->post(env('API_SAP_URL'), [
                'json' => json_encode([
                    'accion' => 'crear_Attachments2',
                    'usuario' => env('API_SAP_USER', 'dani'),
                    'datos' => ['Attachments2_Lines' => $lines]
                ])
            ]);

            $body = $response->json();
            if ($response->successful() && isset($body['AbsoluteEntry'])) {
                Log::info("Entrada de anexos creada en SAP con AbsoluteEntry: {$body['AbsoluteEntry']}");
                return $body['AbsoluteEntry'];
            } else {
                Log::error("Falló la creación de la entrada de anexos en SAP Attachments2.", ['response_body' => $body]);
            }
        } catch (\Exception $e) {
            Log::error("Excepción al llamar a la API para crear Attachments2.", ['exception' => $e->getMessage()]);
        }

        return null;
    }

    private function renderFormWithError(Request $request, array $errors)
    {
        $parteData = $request->all(); // Mantiene los datos del formulario
        $clienteData = [];

        if (!empty($parteData['CustomerCode'])) {
            $clienteData = $this->consultarClientes($parteData['CustomerCode']);
        }

        // Si no se encontró cliente pero hay nombre (cliente contado), lo reconstruimos
        if (empty($clienteData) && !empty($parteData['CustomerName'])) {
            $clienteData = [
                [
                    'CardCode' => $parteData['CustomerCode'] ?? 'CONTADO',
                    'CardName' => $parteData['CustomerName'],
                    'FederalTaxID' => $parteData['FederalTaxID'] ?? '',
                ]
            ];
        }

        $tecnico = $this->nombreTecnico($parteData['TechnicianCode'] ?? '');

        return view('parteFormulario', [
            'parte' => $parteData,
            'cliente' => $clienteData[0] ?? null,
            'origenes' => $this->consultarOrigen(),
            'tecnico' => $tecnico
        ])->withErrors($errors);
    }

    public function consultarOrigen()
    {
        return Cache::remember('sap.origenes', 3600, function () {
            $accion = "consulta_ServiceCallsOrigins";
            $data = [
                'select' => "OriginID,Name",
                'where' => "OriginID gt 0",
                'order' => "Name"
            ];

            try {
                Log::info('Consultando origenes desde SAP (SIN CACHÉ)', ['accion' => $accion]);
                $response = Http::asForm()->post(env('API_SAP_URL'), [
                    'json' => json_encode([
                        'accion' => $accion,
                        'usuario' => env('API_SAP_USER', 'dani'),
                        'datos' => $data
                    ])
                ]);
                $result = $response->json();
                return $result['value'] ?? [];
            } catch (\Exception $e) {
                Log::error('Excepción al consultar la lista de origenes', ['exception' => $e->getMessage()]);
                return [];
            }
        });
    }

    private function extraerMensajeErrorSAP($body)
    {
        $decoded = is_string($body) ? json_decode($body, true) : $body;
        if (isset($decoded['resultado']['error']['message']['value'])) {
            return $decoded['resultado']['error']['message']['value'];
        }
        return $decoded['error']['message']['value'] ?? 'Respuesta inesperada de SAP.';
    }

    private function validarCamposSAP(Request $request)
    {
        return $request->validate([
            'CustomerName' => 'required|string',
            'ItemCode' => 'required|string',
            'ServiceBPType' => 'required|string',
            'Subject' => 'required|string',
            'U_H8_MOTIVO' => 'required|string'
        ], [
            'CustomerName.required' => 'El nombre del cliente es obligatorio.',
            'ItemCode.required' => 'El código del artículo es obligatorio.',
            'ServiceBPType.required' => 'El tipo de interlocutor comercial es obligatorio.',
            'Subject.required' => 'El asunto es obligatorio.',
            'U_H8_MOTIVO.required' => 'El tipo de llamada es obligatorio.'
        ]);
    }

    public function nombreTecnico($id)
    {
        if (empty($id)) {
            return null;
        }

        return Cache::remember('sap.tecnico.' . $id, 3600, function () use ($id) {
            $accion = "consulta_EmployeesInfo";
            $data = [
                "select" => "FirstName",
                "where" => "EmployeeID eq $id",
            ];

            try {
                Log::info('Enviando datos a SAP para obtener técnico (SIN CACHÉ)', ['accion' => $accion, 'datos' => $data]);
                $response = Http::asForm()->post(env('API_SAP_URL'), [
                    'json' => json_encode([
                        'accion' => $accion,
                        'usuario' => env('API_SAP_USER', 'dani'),
                        'datos' => $data
                    ])
                ]);
                $body = $response->json();
                return ($body['value'][0] ?? null);
            } catch (\Exception $e) {
                Log::error('Excepción al consultar técnico en SAP', ['exception' => $e->getMessage()]);
                return null;
            }
        });
    }

    public function vistaImprimir($parteID, $clienteID)
    {
        $parte = $this->consultarPartes($parteID, 'ServiceCallID');
        $cliente = $this->consultarClientes($clienteID);

        $tecnico = null;
        if (!empty($parte) && isset($parte[0]['TechnicianCode'])) {
            $tecnico = $this->nombreTecnico($parte[0]['TechnicianCode']);
        }

        $origen = $this->consultarOrigen();

        return view('partes.vista_imprimir', [
            'parte' => $parte[0] ?? null,
            'cliente' => $cliente[0] ?? null,
            'tecnico' => $tecnico,
            'origen' => $origen
        ]);
    }
}
