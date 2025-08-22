<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OpenAiToolsService;

class TestOpenAiTools extends Command
{
    protected $signature = 'openai:test-tools {--origen=} {--destino=} {--naviera=} {--buscar=} {--precios} {--tipo=}';
    protected $description = 'Prueba las herramientas de OpenAI para rutas y destinos';

    public function handle()
    {
        $this->info('🚢 Probando herramientas de OpenAI para rutas y destinos...');
        
        $origen = $this->option('origen');
        $destino = $this->option('destino');
        $naviera = $this->option('naviera');
        $buscar = $this->option('buscar');
        $precios = $this->option('precios');
        $tipo = $this->option('tipo');

        if ($precios) {
            $this->info("💰 Obteniendo precios y tarifas:");
            if ($origen && $destino) {
                $this->line("  - Ruta: $origen → $destino");
                if ($tipo) $this->line("  - Tipo: $tipo");
                $resultado = OpenAiToolsService::obtenerPreciosRuta($origen, $destino, $tipo);
            } else {
                $this->line("  - Todas las tarifas disponibles");
                $resultado = OpenAiToolsService::obtenerPreciosTarifas();
            }
        } elseif ($buscar) {
            $this->info("🔍 Buscando rutas que contengan: $buscar");
            $resultado = OpenAiToolsService::buscarRutasPorTexto($buscar);
        } else {
            $this->info("📋 Obteniendo rutas con filtros:");
            if ($origen) $this->line("  - Origen: $origen");
            if ($destino) $this->line("  - Destino: $destino");
            if ($naviera) $this->line("  - Naviera: $naviera");
            
            $resultado = OpenAiToolsService::obtenerRutasDestinos($origen, $destino, $naviera);
        }

        $datos = json_decode($resultado, true);
        
        if (!$datos) {
            $this->error('❌ Error al decodificar JSON');
            return Command::FAILURE;
        }

        if (isset($datos['error'])) {
            $this->error("❌ Error: {$datos['error']}");
            return Command::FAILURE;
        }

        // Mostrar resumen
        if (isset($datos['destinos_por_origen'])) {
            $totalOrigenes = count($datos['destinos_por_origen']);
            $totalRutas = 0;
            
            foreach ($datos['destinos_por_origen'] as $codigo => $origenInfo) {
                $totalRutas += count($origenInfo['destinos_disponibles']);
            }
            
            $this->info("✅ Encontrados $totalOrigenes puertos de origen con $totalRutas rutas totales");
            
            // Mostrar detalles
            foreach ($datos['destinos_por_origen'] as $codigo => $origenInfo) {
                $this->line("\n📍 {$origenInfo['origen_nombre']} ($codigo):");
                
                foreach ($origenInfo['destinos_disponibles'] as $destino) {
                    $navieras = implode(', ', $destino['navieras']);
                    $this->line("  └─ {$destino['nombre']} ({$destino['codigo']})");
                    $this->line("     ⏱️  {$destino['duracion']} | 📅 {$destino['frecuencia']}");
                    $this->line("     🚢 {$navieras}");
                }
            }
        }

        // Mostrar información adicional
        if (isset($datos['navieras'])) {
            $this->line("\n🚢 Navieras disponibles: " . implode(', ', $datos['navieras']));
        }

        if (isset($datos['puertos_origen'])) {
            $this->line("\n🏠 Puertos de origen disponibles: " . count($datos['puertos_origen']));
        }

        if (isset($datos['destinos_disponibles'])) {
            $this->line("🎯 Destinos disponibles: " . count($datos['destinos_disponibles']));
        }

        // Mostrar advertencia de precios si existe
        if (isset($datos['advertencia_precios'])) {
            $this->warn("\n⚠️  " . $datos['advertencia_precios']['mensaje']);
        }

        // Mostrar información de precios específicos
        if (isset($datos['precio_especifico'])) {
            $precio = $datos['precio_especifico'];
            $this->info("\n💰 Precio específico:");
            $this->line("  - Tipo: {$precio['tipo']}");
            $this->line("  - Precio: {$precio['precio']}€");
            $this->line("  - Moneda: {$precio['moneda']}");
        }

        // Mostrar información de ruta con precios
        if (isset($datos['ruta'])) {
            $ruta = $datos['ruta'];
            $this->info("\n💰 Precios para {$ruta['ruta_nombre']}:");
            foreach ($ruta as $key => $value) {
                if ($key !== 'ruta_nombre' && $key !== 'navieras' && is_numeric($value)) {
                    $this->line("  - {$key}: {$value}€");
                }
            }
            if (isset($ruta['navieras'])) {
                $this->line("  - Navieras: " . implode(', ', $ruta['navieras']));
            }
        }

        $this->info("\n✅ Prueba completada exitosamente");
        return Command::SUCCESS;
    }
}
