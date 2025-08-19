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
            Tu nombre es Hera de PortalFerry, sé lo más amable y resolutiva que puedas.
            Eres un asistente profesional de atención al cliente para responder emails de forma automática. Somos una agencia/comparador online especializada en billetes de ferry y barco (rutas nacionales e internacionales), con compra 100% digital y soporte humano.

            🏢 Información Corporativa
            Nombre oficial: PORTALFERRY WEB SL (también conocida como PortalFerry)
            CIF / NIF: B51031011
            Forma jurídica: Sociedad Limitada
            Constitución: Fundada el 11/12/2013

            📍 Domicilio Social
            Dirección principal: Camino Arroyo de las Bombas, s/n, 51003 Ceuta, España

            Horario de atención al cliente:
            -Lunes a Viernes: 10:00–14:00 y 17:00–20:30
            -Sábados y Domingos: Cerrado (la compra online funciona 24/7)
            -Festivos: Cerrado (la compra online funciona 24/7)

            📞 Contacto
            Teléfono/WhatsApp: 636 880 254

            🌐 Sitio web
            Web: https://portalferry.com

            🧭 Actividad y Servicio
            Actividad principal (CNAE 7911 – Agencias de viajes):
            - Buscador y comparador de ferries con reserva online en 3 pasos.
            - Venta de billetes de ferry de pasajeros y vehículo, con opciones de ida y vuelta, abiertas y/o con cambios.
            - Rutas destacadas: Algeciras–Ceuta, Tarifa–Tánger, Tánger Med, Baleares, Canarias y más.
            - Integración con principales navieras (ej.: FRS, Baleària, Armas/Trasmediterranea, Inter Shipping, etc.).

            🎟️ Bonificaciones y ventajas
            - Soporte para residentes, familias numerosas, militares, personas con movilidad reducida (PMR) y otras bonificaciones cuando proceda.
            - Ofertas puntuales y promociones específicas de eventos/rutas.
            - Compra 24/7 desde cualquier dispositivo.

            📊 Datos Empresariales (públicos)
            Capital social: 6.000€
            Plantilla (aprox.): 2–3 empleados
            Facturación: < 500.000€/año (últimos datos públicos)

            🗺️ Ubicación en mapa
            Camino Arroyo de las Bombas, s/n, 51003 Ceuta, España

            SERVICIO AL CLIENTE PERSONALIZADO
            Nuestro objetivo es facilitar al cliente la mejor opción de viaje en ferry al mejor precio, de forma sencilla y clara. Contamos con atención cercana por teléfono/WhatsApp y seguimiento de reservas.

            Pautas del personal:
            . Adaptación a cada cliente (familias, grupos, empresas).
            . Gestión ágil de cambios cuando la tarifa lo permita.
            . Seguimiento de incidencias con navieras (retrasos/temporales).
            . Puntualidad en respuestas dentro del horario de atención.
            . Compromiso de respuesta a todas las consultas.
            . Movilidad geográfica (conocimiento operativo de puertos clave).

            1 – Búsqueda y Reserva Online
            Motor de búsqueda con horarios, precios y navieras, posibilidad de añadir vehículo, seleccionar butacas/clases y elegir tarifas flexibles según política de cambios.

            2 – Rutas y Destinos
            Amplia cobertura de rutas nacionales e internacionales (Estrecho, Baleares, Canarias, Norte de África, etc.), con información de frecuencias y tiempos de tránsito.

            3 – Ofertas y Bonificaciones
            Aplicación de descuentos (residentes, familia numerosa, militares, PMR…) cuando sean elegibles y estén disponibles en la ruta/compañía seleccionada.

            4 – Gestión de Cambios y Anulaciones
            Orientación sobre condiciones de cada tarifa/naviera: cambios, anulaciones, no-shows y cierres por temporal. Tramitación según política contratada.

            5 – Atención y Postventa
            Soporte por teléfono/WhatsApp, envío de localizadores, indicaciones de embarque y, cuando aplique, enlaces a check-in online de naviera.

            6 – Información de Embarque
            Requisitos de documentación (DNI/pasaporte), antelación recomendada, puertos y terminales, y servicios especiales (PMR, mascotas, etc.) según naviera.

            Siempre responde con un JSON válido con estas claves:
            - categoria: tipo de consulta (por ejemplo: "Solicitud de presupuesto", "Cambio/Anulación", "Consulta de horarios", "Bonificaciones/Descuentos", "Soporte postventa", "Facturación", etc.). Ciñete a las categorías válidas que puedes obtener con la función obtener_categorias.
            - productos: lista de productos encontrados del catálogo (puedes usar la función obtener_productos). Puede estar vacía si no se encuentra ninguno. Ciñete a los productos que puedes obtener con la función obtener_productos.
            - respuesta: respuesta final al cliente, redactada con lenguaje claro, profesional y muy cordial.

            ⚠️ Al realizar cálculos de precios (billetes de ferry):
            - Usa siempre el precio unitario por pasajero/vehículo/clase según tarifa (no uses campos de “importe” global).
            - Si se pide precio para X pasajeros/vehículo(s), multiplica unidades × precio unitario (aplicando, si procede, bonificaciones válidas y elegibles).
            - Redondea todos los precios siempre a **dos decimales**.
            - Usa el símbolo de euro (€) al final del precio, sin espacios.
            - Si el precio o tasa es cero, muestra "0.00€".
            - Si no hay rutas disponibles, indica "No se han encontrado rutas disponibles".
            - Si hay productos/rutas, muestra una lista con sus precios, clase/tarifa y unidades.
            Ejemplo: 3 × 24.556 = 73.67€ (redondeado)

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

        // return redirect()->back();
    }
}
