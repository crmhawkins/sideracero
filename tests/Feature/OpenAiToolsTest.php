<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\OpenAiToolsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OpenAiToolsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test para verificar que se puede obtener información de rutas sin filtros
     */
    public function test_obtener_rutas_destinos_sin_filtros()
    {
        $resultado = OpenAiToolsService::obtenerRutasDestinos();
        
        $this->assertIsString($resultado);
        
        $datos = json_decode($resultado, true);
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('destinos_por_origen', $datos);
        $this->assertArrayHasKey('navieras', $datos);
    }

    /**
     * Test para verificar filtrado por origen
     */
    public function test_obtener_rutas_destinos_por_origen()
    {
        $resultado = OpenAiToolsService::obtenerRutasDestinos('ALG');
        
        $datos = json_decode($resultado, true);
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('destinos_por_origen', $datos);
        $this->assertArrayHasKey('ALG', $datos['destinos_por_origen']);
        $this->assertEquals('ALGECIRAS', $datos['destinos_por_origen']['ALG']['origen_nombre']);
    }

    /**
     * Test para verificar filtrado por naviera
     */
    public function test_obtener_rutas_destinos_por_naviera()
    {
        $resultado = OpenAiToolsService::obtenerRutasDestinos(null, null, 'Baleària');
        
        $datos = json_decode($resultado, true);
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('destinos_por_origen', $datos);
        
        // Verificar que todas las rutas incluyen Baleària
        foreach ($datos['destinos_por_origen'] as $origen) {
            foreach ($origen['destinos_disponibles'] as $destino) {
                $this->assertContains('Baleària', $destino['navieras']);
            }
        }
    }

    /**
     * Test para verificar filtrado por destino
     */
    public function test_obtener_rutas_destinos_por_destino()
    {
        $resultado = OpenAiToolsService::obtenerRutasDestinos(null, 'CEU');
        
        $datos = json_decode($resultado, true);
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('destinos_por_origen', $datos);
        
        // Verificar que todas las rutas van a Ceuta
        foreach ($datos['destinos_por_origen'] as $origen) {
            foreach ($origen['destinos_disponibles'] as $destino) {
                $this->assertEquals('CEU', $destino['codigo']);
            }
        }
    }

    /**
     * Test para verificar filtrado combinado
     */
    public function test_obtener_rutas_destinos_filtrado_combinado()
    {
        $resultado = OpenAiToolsService::obtenerRutasDestinos('ALG', 'CEU', 'FRS');
        
        $datos = json_decode($resultado, true);
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('destinos_por_origen', $datos);
        $this->assertArrayHasKey('ALG', $datos['destinos_por_origen']);
        
        $destinos = $datos['destinos_por_origen']['ALG']['destinos_disponibles'];
        $this->assertNotEmpty($destinos);
        
        foreach ($destinos as $destino) {
            $this->assertEquals('CEU', $destino['codigo']);
            $this->assertContains('FRS', $destino['navieras']);
        }
    }

    /**
     * Test para verificar búsqueda por texto
     */
    public function test_buscar_rutas_por_texto()
    {
        $resultado = OpenAiToolsService::buscarRutasPorTexto('algeciras');
        
        $datos = json_decode($resultado, true);
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('destinos_por_origen', $datos);
        $this->assertArrayHasKey('ALG', $datos['destinos_por_origen']);
    }

    /**
     * Test para verificar que se puede obtener información de precios
     */
    public function test_obtener_precios_tarifas()
    {
        $resultado = OpenAiToolsService::obtenerPreciosTarifas();
        
        $this->assertIsString($resultado);
        
        // Si el archivo no existe, debería devolver un error
        if (strpos($resultado, 'error') !== false) {
            $this->assertStringContainsString('error', $resultado);
        } else {
            $datos = json_decode($resultado, true);
            $this->assertNotNull($datos);
        }
    }

    /**
     * Test para verificar que se incluye la advertencia de precios
     */
    public function test_advertencia_precios_incluida()
    {
        $resultado = OpenAiToolsService::obtenerPreciosTarifas();
        $datos = json_decode($resultado, true);
        
        $this->assertArrayHasKey('advertencia_precios', $datos);
        $this->assertArrayHasKey('mensaje', $datos['advertencia_precios']);
        $this->assertArrayHasKey('tipo', $datos['advertencia_precios']);
        $this->assertArrayHasKey('importante', $datos['advertencia_precios']);
        
        $this->assertEquals('advertencia', $datos['advertencia_precios']['tipo']);
        $this->assertTrue($datos['advertencia_precios']['importante']);
        $this->assertStringContainsString('orientativos', $datos['advertencia_precios']['mensaje']);
        $this->assertStringContainsString('cambios', $datos['advertencia_precios']['mensaje']);
    }

    /**
     * Test para verificar precios específicos de una ruta
     */
    public function test_obtener_precios_ruta_especifica()
    {
        $resultado = OpenAiToolsService::obtenerPreciosRuta('ALG', 'CEU');
        $datos = json_decode($resultado, true);
        
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('advertencia_precios', $datos);
        $this->assertArrayHasKey('ruta', $datos);
        $this->assertEquals('ALGECIRAS-CEUTA', $datos['ruta']['ruta_nombre']);
    }

    /**
     * Test para verificar precio específico por tipo
     */
    public function test_obtener_precio_especifico_por_tipo()
    {
        $resultado = OpenAiToolsService::obtenerPreciosRuta('ALG', 'CEU', 'pasajero_ida');
        $datos = json_decode($resultado, true);
        
        $this->assertNotNull($datos);
        $this->assertArrayHasKey('advertencia_precios', $datos);
        $this->assertArrayHasKey('precio_especifico', $datos);
        $this->assertEquals('pasajero_ida', $datos['precio_especifico']['tipo']);
        $this->assertEquals('EUR', $datos['precio_especifico']['moneda']);
        $this->assertIsNumeric($datos['precio_especifico']['precio']);
    }

    /**
     * Test para verificar cálculo de precios con bonificaciones
     */
    public function test_calcular_precio_con_bonificaciones()
    {
        $precioBase = 100.00;
        $bonificaciones = [
            ['tipo' => 'residente_ceuta', 'descuento' => 0.50]
        ];
        
        $resultado = OpenAiToolsService::calcularPrecioConBonificaciones($precioBase, $bonificaciones);
        
        $this->assertEquals(100.00, $resultado['precio_base']);
        $this->assertEquals(50.00, $resultado['precio_final']);
        $this->assertArrayHasKey('advertencia_precios', $resultado);
        $this->assertCount(1, $resultado['bonificaciones_aplicadas']);
    }
}
