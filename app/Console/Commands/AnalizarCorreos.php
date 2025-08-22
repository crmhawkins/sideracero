<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CorreoEntrante;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAiToolsService;

class AnalizarCorreos extends Command
{
    protected $signature = 'correos:analizar';
    protected $description = 'Analiza correos no analizados usando GPT-4o con tools';

    public function handle()
    {
        $apiKey = env('OPENAI_API_KEY');
        $modelo = 'gpt-4o';
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        $correos = CorreoEntrante::where('analizado', false)->limit(5)->get();

        if ($correos->isEmpty()) {
            $this->info('No hay correos pendientes de analizar.');
            return Command::SUCCESS;
        }

        foreach ($correos as $correo) {
            $this->info("Analizando: " . $correo->asunto);


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
                        "name" => "obtener_rutas_destinos",
                        "description" => "Devuelve información completa sobre rutas marítimas, puertos de origen, destinos disponibles, navieras, duraciones y frecuencias de ferry",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "origen" => [
                                    "type" => "string",
                                    "description" => "Código del puerto de origen (ej: ALG, BCN, VLC, etc.) o 'todos' para obtener todas las rutas"
                                ],
                                "destino" => [
                                    "type" => "string", 
                                    "description" => "Código del puerto de destino (ej: CEU, PMI, TCI, etc.) o 'todos' para obtener todos los destinos"
                                ],
                                "naviera" => [
                                    "type" => "string",
                                    "description" => "Nombre de la naviera específica (ej: Baleària, FRS, Armas/Trasmediterranea) o 'todas' para todas las navieras"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "obtener_precios_tarifas",
                        "description" => "Devuelve precios y tarifas de billetes de ferry con advertencia de que los precios pueden cambiar hasta el pago",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "origen" => [
                                    "type" => "string",
                                    "description" => "Código del puerto de origen (ej: ALG, BCN, VLC, etc.)"
                                ],
                                "destino" => [
                                    "type" => "string",
                                    "description" => "Código del puerto de destino (ej: CEU, PMI, TCI, etc.)"
                                ],
                                "tipo" => [
                                    "type" => "string",
                                    "description" => "Tipo de tarifa (ej: pasajero_ida, vehiculo_pequeño_ida, mascota, etc.)"
                                ]
                            ]
                        ]
                    ]
                ]
            ];


            $messages = [
                ["role" => "system", "content" => "Eres un asistente que analiza correos y devuelve un JSON con categoría, productos y respuesta."],
                ["role" => "user", "content" => $prompt],
            ];

            $response = Http::withToken($apiKey)->post($endpoint, [
                'model' => $modelo,
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => "auto",
            ]);

            if ($response->failed()) {
                $this->error("❌ Error llamando a OpenAI: " . $response->body());
                continue;
            }

            $data = $response->json();

            if (isset($data['choices'][0]['message']['tool_calls'])) {
                $toolCall = $data['choices'][0]['message']['tool_calls'][0];
                $toolName = $toolCall['function']['name'];
                $toolCallId = $toolCall['id'];

                $simulatedToolResponse = match ($toolName) {
                    'obtener_rutas_destinos' => $this->procesarRutasDestinos($toolCall['function']['arguments'] ?? '{}'),
                    'obtener_precios_tarifas' => $this->procesarPreciosTarifas($toolCall['function']['arguments'] ?? '{}'),
                    default => null,
                };

                if (!$simulatedToolResponse) {
                    $this->warn("⚠ Tool no reconocida: $toolName");
                    continue;
                }

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

                if ($responseFinal->failed()) {
                    $this->error("❌ Error en segunda llamada OpenAI: " . $responseFinal->body());
                    continue;
                }

                $contenido = $responseFinal->json('choices.0.message.content');
            } else {
                $contenido = $data['choices'][0]['message']['content'];
            }

            if (preg_match('/\{.*\}/s', $contenido, $matches)) {
                $contenido = $matches[0];
            }

            $datos = json_decode($contenido, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $correo->productos_detectados = $datos['productos'] ?? [];
                $correo->categoria = $datos['categoria'] ?? 'Sin categorizar';
                $correo->respuesta_sugerida = $datos['respuesta'] ?? '';
                $correo->analizado = true;
                $correo->save();

                $this->info("✓ Correo analizado: {$correo->categoria}");
            } else {
                $this->warn("⚠ No se pudo interpretar la respuesta JSON del correo: {$correo->id}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Procesa las consultas de rutas y destinos con filtros
     */
    private function procesarRutasDestinos($arguments)
    {
        $params = json_decode($arguments, true);
        
        if (!$params) {
            return OpenAiToolsService::obtenerRutasDestinos();
        }

        $origen = $params['origen'] ?? null;
        $destino = $params['destino'] ?? null;
        $naviera = $params['naviera'] ?? null;

        return OpenAiToolsService::obtenerRutasDestinos($origen, $destino, $naviera);
    }

    /**
     * Procesa las consultas de precios y tarifas con filtros
     */
    private function procesarPreciosTarifas($arguments)
    {
        $params = json_decode($arguments, true);
        
        if (!$params) {
            return OpenAiToolsService::obtenerPreciosTarifas();
        }

        $origen = $params['origen'] ?? null;
        $destino = $params['destino'] ?? null;
        $tipo = $params['tipo'] ?? null;

        if ($origen && $destino) {
            return OpenAiToolsService::obtenerPreciosRuta($origen, $destino, $tipo);
        }

        return OpenAiToolsService::obtenerPreciosTarifas();
    }
}
