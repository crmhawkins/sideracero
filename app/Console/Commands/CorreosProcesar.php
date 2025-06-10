<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CorreosProcesar extends Command
{
    protected $signature = 'correos:procesar';
    protected $description = 'Lee, analiza y responde correos secuencialmente si corresponde';

    public function handle()
    {
        $this->info('📩 Ejecutando: correos:leer');
        $leer = Artisan::call('correos:leer');

        if ($leer === 0) {
            $this->info('🧠 Ejecutando: correos:analizar');
            $analizar = Artisan::call('correos:analizar');

            if ($analizar === 0) {
                $this->info('✉️ Ejecutando: correos:responder');
                Artisan::call('correos:responder');
            } else {
                $this->error('❌ Falló el análisis de correos.');
            }
        } else {
            $this->error('❌ Falló la lectura de correos.');
        }

        return 0;
    }
}
