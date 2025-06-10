<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CorreoEntrante;
use Illuminate\Support\Facades\Mail;

class ResponderCorreos extends Command
{
    protected $signature = 'correos:responder';
    protected $description = 'Envía respuestas automáticas a correos analizados con sugerencia de respuesta.';

    public function handle()
    {
        $correos = CorreoEntrante::where('analizado', true)
            ->where('respuesta_sugerida', '!=', '')
            ->where('respondido', false)
            ->get();

        if ($correos->isEmpty()) {
            $this->info("No hay correos pendientes de responder.");
            return Command::SUCCESS;
        }

        foreach ($correos as $correo) {
            try {
                Mail::raw($correo->respuesta_sugerida, function ($message) use ($correo) {
                    $message->to($correo->remitente)
                            ->subject("Re: " . $correo->asunto);
                });

                $correo->respondido = true;
                $correo->save();

                $this->info("✉️ Respuesta enviada a: {$correo->remitente}");

            } catch (\Exception $e) {
                $this->error("❌ Error enviando a {$correo->remitente}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
