<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CorreoEntrante;
use Illuminate\Support\Facades\Storage;

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
            Tu nombre es Hera de SiderAcero, se lo mas amable y resulutiva que puedas.
            Eres un asistente profesional de atención al cliente, estas para responder a emails, con lo cual tu respuesta debe venir preparada para contestar automaticamente el email, somos una empresa especializada en productos metálicos industriales, especialmente tubos de acero al carbono sin costura.

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
            - categoria: tipo de consulta (por ejemplo: "Solicitud de presupuesto", "Consulta técnica", etc.) Ciñete a las categorías válidas que puedes obtener con la función obtener_categorias.
            - productos: lista de productos encontrados del catálogo (puedes usar la función obtener_productos). Puede estar vacía si no se encuentra ninguno. Ciñete a los productos que puedes obtener con la función obtener_productos.
            - respuesta: respuesta final al cliente, redactada con lenguaje claro, profesional y muy cordial.

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
                        "description" => "Devuelve una lista de categorías válidas para clasificar los correos",
                        "parameters" => [
                            "type" => "object",
                            "properties" => new \stdClass()
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
                    'obtener_productos' => Storage::get('openai/productos.json'),
                    'obtener_categorias' => json_encode([
                        "Solicitud de presupuesto", "Consulta técnica", "Petición de información", "Incidencia postventa", "Otro"
                    ]),
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
}
