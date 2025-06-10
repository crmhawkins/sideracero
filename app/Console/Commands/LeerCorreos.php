<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\CorreoEntrante;

class LeerCorreos extends Command
{
    protected $signature = 'correos:leer';
    protected $description = 'Lee correos del servidor IMAP y los guarda en la base de datos';

    public function handle()
    {
        $this->info('Conectando al servidor IMAP...');

        try {
            $client = Client::account('default');
            $client->connect();

            $inbox = $client->getFolder('INBOX');
            $messages = $inbox->messages()->unseen()->limit(10)->get();

            $this->info('Correos no leídos: ' . $messages->count());

            foreach ($messages as $message) {
                $rawSubject = $message->getHeader()->get('subject');
                $asunto = $this->decodeMimeHeader((string) $rawSubject);


                $correo = new CorreoEntrante();
                $correo->remitente = $message->getFrom()[0]->mail ?? '';
                $correo->asunto = $asunto;
                $correo->cuerpo = $message->getTextBody();
                $correo->fecha = $message->getDate();
                $correo->leido = false;

                $correo->save();
                // ✅ Marcar como leído
                $message->setFlag('Seen');
                
                $this->info("Correo guardado: " . $correo->asunto);
            }

        } catch (\Exception $e) {
            $this->error('Error al conectar o procesar: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }

    private function decodeMimeHeader($header)
    {
        preg_match_all('/=\?([^?]+)\?([BQ])\?([^?]+)\?=/i', $header, $matches, PREG_SET_ORDER);
        $result = '';

        foreach ($matches as $m) {
            $charset = strtoupper($m[1]);
            $encoding = strtoupper($m[2]);
            $encodedText = $m[3];

            if ($encoding === 'B') {
                $text = base64_decode($encodedText);
            } elseif ($encoding === 'Q') {
                $text = quoted_printable_decode(str_replace('_', ' ', $encodedText));
            } else {
                $text = $encodedText;
            }

            $result .= mb_convert_encoding($text, 'UTF-8', $charset);
        }

        return $result ?: $header;
    }

}
