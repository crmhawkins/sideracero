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

            $prompt = <<<EOT
            Analiza el siguiente correo y responde solo con un JSON válido. El JSON debe tener estas claves:
            - categoria (ej: "Solicitud de presupuesto", "Consulta técnica", etc.)
            - productos (lista opcional de productos relacionados con nuestro catálogo, si no encuentras el producto en el catalogo, déjalo vacío)
            - respuesta (respuesta breve y profesional al cliente, si no esta relacionado con nuestra actividad ni respondas, déjalo vacío)

            Asunto: {$correo->asunto}
            Cuerpo: {$correo->cuerpo}
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
