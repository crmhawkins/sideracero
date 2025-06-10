<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatGpt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ChatGptController extends Controller
{
    public function index()
    {
        $mensajes = ChatGpt::orderBy('created_at')->get();
        return view('chat.index', compact('mensajes'));
    }

    public function enviar(Request $request)
    {
        $request->validate(['mensaje' => 'required']);
        $mensaje = $request->input('mensaje');

        $chat = ChatGpt::create([
            'mensaje' => $mensaje,
        ]);

        $apiKey = env('OPENAI_API_KEY');
        $modelo = 'gpt-4o';
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $fechaHoy = now()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
        $diaSemanaHoy = now()->locale('es')->isoFormat('dddd');
        $diaSemanaManiana = now()->addDay()->locale('es')->isoFormat('dddd');

        $prompt = <<<EOT
            Hoy es $diaSemanaHoy, $fechaHoy. Mañana es $diaSemanaManiana.
            Eres un asistente profesional de atención al cliente para una empresa especializada en productos metálicos industriales, especialmente tubos de acero al carbono sin costura.

            🏢 Información Corporativa
            Nombre oficial: SIDERACERO DISTRIBUIDORA ANDALUZA DE HIERROS SA (también conocida como SIDERACERO)
            CIF / NIF: A11769502
            Forma jurídica: Sociedad Anónima
            Constitución: Fundada el 19/01/2004

            📍 Domicilio Social
            Dirección principal: Avda. Mar Cantábrico, Parcela 1, P.I. Las Marismas de Palmones, 11370 Los Barrios (Cádiz), España

            Horario:
            -Lunes 7:30 a 15:30
            -Martes 7:30 a 15:30
            -Miércoles 7:30 a 15:30
            -Jueves 7:30 a 15:30
            -Viernes 7:30 a 15:30
            -Sábados y Domingos: Cerrado
            -Festivos: Cerrado

            📞 Contacto
            Teléfono principal: 956 676 290

            🌐 Sitio web
            Web: www.sideracero.com

            🧰 Actividad y Servicio
            Actividad principal:

            - Comercio al por mayor de metales y minerales metálicos (CNAE 4672).
            - Especializada en corte, transformación y comercialización de productos siderúrgicos, con servicios de oxicorte y plasma.
            - Clasificación SIC: 5051 – Metales


            📊 Datos Empresariales (según Axesor)
            Capital social: Entre 100 000 y 1 000 000 €
            Plantilla: Aproximadamente 29 empleados
            Volumen de ventas: Entre 3 y 50 M€


            🗺️ Ubicación en mapa
            Puedes visitar la sede principal aquí:
            Calle Mar Cantábrico, P.I. Las Marismas de Palmones 1, 11370 Los Barrios, Cádiz, España

            SERVICIOS AL CLIENTE PERSONALIZADO
            Nuestro principal objetivo es colaborar con nuestros clientes aportando valor a su actividad diaría. Para ello contamos con una plantilla, que por su preparacion y experiencia asesorán eficazmente a nuestros clientes satisfaciendo sus necesidades y reforzando nuestra relación con ellos basada en la Confianza y el Compromiso Mutuo.

            Las pautas a seguir por nuestro personal son:

            . Adaptación al cliente.
            . Servicio exprés 24 horas en corte de chapas.
            . Seguimiento constante de todas las Operaciones Comerciales.
            . Puntualidad en el cumplimiento de plazos
            . Compromiso de respuesta a la totalidad de las consultas.
            . Movilidad Geográfica a nivel Nacional.

            1-Oxicorte y Plasma
            A razón de las características de la máquina de plasma-oxicorte nos permite el corte y marcado de chapas de cualquier índole y calidad.

            El sistema de corte nos permite diseñar cualquier figura que se pretenda fabricar, ya sea en acero al carbono, inoxidable, aluminio, titanio, aleaciones, chapas especiales e incluso chapones de hasta 400 mm de espesor.

            Entre otras realizamos piezas tales como mamparos, cuadernas, refuerzos, piezas desarrolladas para calderería, , discos ciegos, bridas, orejetas, arandelas, platabandas, placas de anclaje taladradas, herramientas varias, utillajes, figuras especiales, etc…

            2-Chapas
            Contamos con una amplia Gama de Chapas de Diversas Calidades y acabados, Imprimadas - Chorreadas con Oxido de Zin o Silicato de Zin, Galvanizadas.

            3-Tubos
            En Nuestro Stock contamos con diferentes acabados: Galvanizado o Decapados.

            4-Perfiles Estructurales
            Contamos con una amplia Gama de Vigas.

            5-Tuberias
            Contamos con una amplia Gama de Tuberías.

            6-Llantas Bulbo
            Tenemos una amplia Gama de llantas Bulbo Chorreadas e Imprimadas y Certificas.

            7-Rejillas electrosoldadas
            Contamos con una amplia Gama de Rejillas.

            8-Redondo corrugado
            Contamos con una amplia Gama de stock en calidad B500SD y B400SD.

            9-Malla electrosoldada
            Contamos con una amplia Gama de Stock.

            10-Inoxidable
            Contamos con Una amplia Gama de Material Inoxidable.

            11-Paneles cerramientos
            Contamos con un amplio Gama para los cerramientos de Naves.

            12-Varios
            Tenemos una amplia Gama de llantas Bulbo Chorreadas e Imprimadas y Certificas.


            Siempre responde con un JSON válido con estas claves:
            - categoria: tipo de consulta (por ejemplo: "Solicitud de presupuesto", "Consulta técnica", etc.)
            - productos: lista de productos encontrados del catálogo (puedes usar la función obtener_productos). Puede estar vacía si no se encuentra ninguno.
            - respuesta: respuesta final al cliente, redactada con lenguaje claro, profesional y directo.

            ⚠️ Al realizar cálculos de precios:
            - Usa solo el campo "Precio venta" (precio unitario por metro).
            - Ignora el campo "Importe", ya que es irrelevante para el cálculo.
            - Si se pide precio para cierta cantidad de metros, multiplícalo por el precio por metro.
            - Redondea todos los precios siempre a **dos decimales**.
            - Usa el símbolo de euro (€) al final del precio, sin espacios.
            - Si el precio es cero, muestra "0.00€".
            - Si no hay productos, indica "No se han encontrado productos relacionados".
            - Si hay productos, muestra una lista de ellos con sus precios y unidades.
            Ejemplo: 15 × 25.5463 = 383.20 (redondeado)€

            No uses frases de espera como "te contesto en breve", y no entregues la respuesta fuera del campo `respuesta`.
            EOT;




        $tools = [
            [
                "type" => "function",
                "function" => [
                    "name" => "obtener_productos",
                    "description" => "Devuelve una lista de productos de nuestro catálogo",
                    "parameters" => [
                        "type" => "object",
                        "properties" => new \stdClass()
                    ]
                ]
            ],
            [
                "type" => "function",
                "function" => [
                    "name" => "obtener_categorias",
                    "description" => "Devuelve una lista de categorías válidas para clasificar las conversaciones",
                    "parameters" => [
                        "type" => "object",
                        "properties" => new \stdClass()
                    ]
                ]
            ]
        ];

        // $messages = [
        //     ["role" => "system", "content" => $prompt],
        //     ["role" => "user", "content" => $mensaje],
        // ];
        // Recuperar historial de conversación

        // $historial = ChatGpt::orderBy('created_at', 'desc')->take(10)->get()->reverse();

        $historial = ChatGpt::orderBy('created_at')->get();

        $messages = [
            ["role" => "system", "content" => $prompt],
        ];

        // Añadir mensajes anteriores como contexto
        foreach ($historial as $msg) {
            if ($msg->mensaje) {
                $messages[] = ["role" => "user", "content" => $msg->mensaje];
            }
            if ($msg->respuesta) {
                $messages[] = ["role" => "assistant", "content" => $msg->respuesta];
            }
        }

        // Añadir el mensaje actual
        $messages[] = ["role" => "user", "content" => $mensaje];

        $response = Http::withToken($apiKey)->post($endpoint, [
            'model' => $modelo,
            'messages' => $messages,
            'tools' => $tools,
            'tool_choice' => 'auto',
        ]);

        if ($response->failed()) {
            $chat->respuesta = '❌ Error al contactar con OpenAI: ' . $response->body();
            $chat->save();
            return redirect()->back();
        }

        $data = $response->json();

        // Manejar llamada a tools
        if (isset($data['choices'][0]['message']['tool_calls'])) {
            $toolCall = $data['choices'][0]['message']['tool_calls'][0];
            $toolName = $toolCall['function']['name'];
            $toolCallId = $toolCall['id'];

            $simulatedToolResponse = match ($toolName) {
                'obtener_productos' => Storage::get('openai/productos.json'),
                'obtener_categorias' => json_encode([
                    "Solicitud de presupuesto", "Consulta técnica", "Petición de información", "Incidencia postventa", "Otro"
                ]),
                default => null,
            };

            if ($simulatedToolResponse) {
                $toolMessage = [
                    ["role" => "assistant", "tool_calls" => [$toolCall]],
                    [
                        "role" => "tool",
                        "tool_call_id" => $toolCallId,
                        "content" => $simulatedToolResponse
                    ]
                ];

                $responseFinal = Http::withToken($apiKey)->post($endpoint, [
                    'model' => $modelo,
                    'messages' => array_merge($messages, $toolMessage),
                ]);

                $contenido = $responseFinal->json('choices.0.message.content');
            } else {
                $contenido = $data['choices'][0]['message']['content'];
            }
        } else {
            $contenido = $data['choices'][0]['message']['content'];
        }

        // Extraer y limpiar JSON
        if (preg_match('/\{.*\}/s', $contenido, $matches)) {
            $contenido = $matches[0];
        }

        $datos = json_decode($contenido, true);

        $respuestaFormateada = null; // Declaración por defecto

        // if (json_last_error() === JSON_ERROR_NONE && isset($datos['respuesta'])) {
        //     $respuestaFormateada = nl2br(e($datos['respuesta']));

        //     // Si 'productos' existe y es un string con JSON, intentar decodificarlo
        //     if (!empty($datos['productos'])) {
        //         if (is_string($datos['productos']) && str_starts_with(trim($datos['productos']), '{')) {
        //             $productosDecodificados = json_decode($datos['productos'], true);

        //             if (json_last_error() === JSON_ERROR_NONE) {
        //                 $listaProductos = "<ul class='list-disc list-inside mt-2'>";
        //                 foreach ($productosDecodificados as $clave => $valor) {
        //                     $listaProductos .= "<li><strong>" . e($clave) . ":</strong> " . e((string)$valor) . "</li>";
        //                 }
        //                 $listaProductos .= "</ul>";
        //                 $respuestaFormateada .= "<div class='mt-4 text-sm text-gray-700 dark:text-gray-200'>📦 Productos detectados:" . $listaProductos . "</div>";
        //             }
        //         } elseif (is_array($datos['productos'])) {
        //             $listaProductos = "<ul class='list-disc list-inside mt-2'>";
        //             foreach ($datos['productos'] as $producto) {
        //                 if (is_array($producto)) {
        //                     $linea = "<ul class='ml-4 list-disc'>";
        //                     foreach ($producto as $clave => $valor) {
        //                         $linea .= "<li><strong>" . e($clave) . ":</strong> " . e((string)$valor) . "</li>";
        //                     }
        //                     $linea .= "</ul>";
        //                     $listaProductos .= "<li>$linea</li>";
        //                 } else {
        //                     $listaProductos .= "<li>" . e((string)$producto) . "</li>";
        //                 }
        //             }
        //             $listaProductos .= "</ul>";
        //             $respuestaFormateada .= "<div class='mt-4 text-sm text-gray-700 dark:text-gray-200'>📦 Productos detectados:" . $listaProductos . "</div>";
        //         }
        //     }

        //     $chat->respuesta = $respuestaFormateada;
        // } else {
        //     $chat->respuesta = nl2br(e($contenido));
        // }

        // $chat->respuesta = $respuestaFormateada;
        if (json_last_error() === JSON_ERROR_NONE && isset($datos['respuesta'])) {
            $respuestaFormateada = nl2br(e($datos['respuesta']));

            // Si 'productos' existe y es un string con JSON, intentar decodificarlo
            if (!empty($datos['productos'])) {
                if (is_string($datos['productos']) && str_starts_with(trim($datos['productos']), '{')) {
                    $productosDecodificados = json_decode($datos['productos'], true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $listaProductos = "<ul class='list-disc list-inside mt-2'>";
                        foreach ($productosDecodificados as $clave => $valor) {
                            $listaProductos .= "<li><strong>" . e($clave) . ":</strong> " . e((string)$valor) . "</li>";
                        }
                        $listaProductos .= "</ul>";
                        $respuestaFormateada .= "<div class='mt-4 text-sm text-gray-700 dark:text-gray-200'>📦 Productos detectados:" . $listaProductos . "</div>";
                    }
                } elseif (is_array($datos['productos'])) {
                    $listaProductos = "<ul class='list-disc list-inside mt-2'>";
                    foreach ($datos['productos'] as $producto) {
                        if (is_array($producto)) {
                            $linea = "<ul class='ml-4 list-disc'>";
                            foreach ($producto as $clave => $valor) {
                                $linea .= "<li><strong>" . e($clave) . ":</strong> " . e((string)$valor) . "</li>";
                            }
                            $linea .= "</ul>";
                            $listaProductos .= "<li>$linea</li>";
                        } else {
                            $listaProductos .= "<li>" . e((string)$producto) . "</li>";
                        }
                    }
                    $listaProductos .= "</ul>";
                    $respuestaFormateada .= "<div class='mt-4 text-sm text-gray-700 dark:text-gray-200'>📦 Productos detectados:" . $listaProductos . "</div>";
                }
            }

            $chat->respuesta = $respuestaFormateada;
        } else {
            $chat->respuesta = nl2br(e($contenido));
        }

        $chat->save();

        return response()->json([
            'html' => view('chat._respuesta', compact('chat'))->render()
        ]);

        return redirect()->back();
    }
}
