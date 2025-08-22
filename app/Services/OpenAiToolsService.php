<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class OpenAiToolsService
{
    /**
     * Obtiene información de rutas y destinos con filtros opcionales
     */
    public static function obtenerRutasDestinos($origen = null, $destino = null, $naviera = null)
    {
        $jsonContent = Storage::get('openai/rutas_destinos.json');
        $data = json_decode($jsonContent, true);

        if (!$data) {
            return json_encode(['error' => 'No se pudo cargar la información de rutas']);
        }

        // Si no hay filtros, devolver toda la información
        if (!$origen && !$destino && !$naviera) {
            return $jsonContent;
        }

        $resultado = [];

        // Filtrar por origen específico
        if ($origen && $origen !== 'todos') {
            if (isset($data['destinos_por_origen'][$origen])) {
                $resultado['destinos_por_origen'][$origen] = $data['destinos_por_origen'][$origen];
                
                // Aplicar filtros adicionales
                if ($destino || $naviera) {
                    $destinosFiltrados = [];
                    foreach ($resultado['destinos_por_origen'][$origen]['destinos_disponibles'] as $destinoInfo) {
                        $incluir = true;
                        
                        if ($destino && $destino !== 'todos' && $destinoInfo['codigo'] !== $destino) {
                            $incluir = false;
                        }
                        
                        if ($naviera && $naviera !== 'todas') {
                            $navieraEncontrada = false;
                            foreach ($destinoInfo['navieras'] as $nav) {
                                if (stripos($nav, $naviera) !== false) {
                                    $navieraEncontrada = true;
                                    break;
                                }
                            }
                            if (!$navieraEncontrada) {
                                $incluir = false;
                            }
                        }
                        
                        if ($incluir) {
                            $destinosFiltrados[] = $destinoInfo;
                        }
                    }
                    $resultado['destinos_por_origen'][$origen]['destinos_disponibles'] = $destinosFiltrados;
                }
            }
        } else {
            // Filtrar todos los orígenes
            $resultado['destinos_por_origen'] = [];
            foreach ($data['destinos_por_origen'] as $codigoOrigen => $origenInfo) {
                $destinosFiltrados = [];
                
                foreach ($origenInfo['destinos_disponibles'] as $destinoInfo) {
                    $incluir = true;
                    
                    if ($destino && $destino !== 'todos' && $destinoInfo['codigo'] !== $destino) {
                        $incluir = false;
                    }
                    
                    if ($naviera && $naviera !== 'todas') {
                        $navieraEncontrada = false;
                        foreach ($destinoInfo['navieras'] as $nav) {
                            if (stripos($nav, $naviera) !== false) {
                                $navieraEncontrada = true;
                                break;
                            }
                        }
                        if (!$navieraEncontrada) {
                            $incluir = false;
                        }
                    }
                    
                    if ($incluir) {
                        $destinosFiltrados[] = $destinoInfo;
                    }
                }
                
                if (!empty($destinosFiltrados)) {
                    $resultado['destinos_por_origen'][$codigoOrigen] = $origenInfo;
                    $resultado['destinos_por_origen'][$codigoOrigen]['destinos_disponibles'] = $destinosFiltrados;
                }
            }
        }

        // Incluir información adicional si está disponible
        if (isset($data['rutas_principales'])) {
            $resultado['rutas_principales'] = $data['rutas_principales'];
        }
        
        if (isset($data['destinos_disponibles'])) {
            $resultado['destinos_disponibles'] = $data['destinos_disponibles'];
        }
        
        if (isset($data['puertos_origen'])) {
            $resultado['puertos_origen'] = $data['puertos_origen'];
        }
        
        if (isset($data['navieras'])) {
            $resultado['navieras'] = $data['navieras'];
        }

        return json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Busca rutas por texto libre (nombre de ciudad, puerto, etc.)
     */
    public static function buscarRutasPorTexto($texto)
    {
        $jsonContent = Storage::get('openai/rutas_destinos.json');
        $data = json_decode($jsonContent, true);

        if (!$data) {
            return json_encode(['error' => 'No se pudo cargar la información de rutas']);
        }

        $texto = strtolower(trim($texto));
        $resultado = [];

        // Buscar en puertos de origen
        foreach ($data['destinos_por_origen'] as $codigo => $origenInfo) {
            $nombreOrigen = strtolower($origenInfo['origen_nombre']);
            
            if (strpos($nombreOrigen, $texto) !== false || strpos($codigo, $texto) !== false) {
                $resultado['destinos_por_origen'][$codigo] = $origenInfo;
            } else {
                // Buscar en destinos de este origen
                $destinosEncontrados = [];
                foreach ($origenInfo['destinos_disponibles'] as $destino) {
                    $nombreDestino = strtolower($destino['nombre']);
                    if (strpos($nombreDestino, $texto) !== false || strpos($destino['codigo'], $texto) !== false) {
                        $destinosEncontrados[] = $destino;
                    }
                }
                
                if (!empty($destinosEncontrados)) {
                    $resultado['destinos_por_origen'][$codigo] = $origenInfo;
                    $resultado['destinos_por_origen'][$codigo]['destinos_disponibles'] = $destinosEncontrados;
                }
            }
        }

        // Incluir información adicional
        if (isset($data['navieras'])) {
            $resultado['navieras'] = $data['navieras'];
        }

        return json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Obtiene información de precios y tarifas
     */
    public static function obtenerPreciosTarifas()
    {
        $jsonContent = Storage::get('openai/precios_tarifas.json');
        
        if (!$jsonContent) {
            return json_encode(['error' => 'No se encontró información de precios y tarifas']);
        }
        
        $datos = json_decode($jsonContent, true);
        
        // Agregar advertencia sobre precios
        $datos['advertencia_precios'] = [
            'mensaje' => 'Los precios mostrados son orientativos y pueden sufrir cambios hasta que se realice el pago final. Los precios finales se confirmarán en el momento de la reserva.',
            'tipo' => 'advertencia',
            'importante' => true
        ];
        
        return json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Obtiene precios específicos para una ruta con advertencia
     */
    public static function obtenerPreciosRuta($origen, $destino, $tipo = null)
    {
        $jsonContent = Storage::get('openai/precios_tarifas.json');
        
        if (!$jsonContent) {
            return json_encode(['error' => 'No se encontró información de precios y tarifas']);
        }
        
        $datos = json_decode($jsonContent, true);
        $rutaKey = strtoupper($origen) . '-' . strtoupper($destino);
        
        $resultado = [
            'advertencia_precios' => [
                'mensaje' => 'Los precios mostrados son orientativos y pueden sufrir cambios hasta que se realice el pago final. Los precios finales se confirmarán en el momento de la reserva.',
                'tipo' => 'advertencia',
                'importante' => true
            ]
        ];
        
        if (isset($datos['tarifas_por_ruta'][$rutaKey])) {
            $resultado['ruta'] = $datos['tarifas_por_ruta'][$rutaKey];
            
            // Si se especifica un tipo, filtrar solo ese tipo
            if ($tipo && isset($resultado['ruta'][$tipo])) {
                $resultado['precio_especifico'] = [
                    'tipo' => $tipo,
                    'precio' => $resultado['ruta'][$tipo],
                    'moneda' => 'EUR'
                ];
            }
        } else {
            $resultado['error'] = "No se encontraron precios para la ruta $origen-$destino";
        }
        
        return json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Calcula precio con bonificaciones aplicadas
     */
    public static function calcularPrecioConBonificaciones($precioBase, $bonificaciones = [])
    {
        $precioFinal = $precioBase;
        $bonificacionesAplicadas = [];
        
        foreach ($bonificaciones as $bonificacion) {
            if (isset($bonificacion['tipo']) && isset($bonificacion['descuento'])) {
                $descuento = $precioBase * $bonificacion['descuento'];
                $precioFinal -= $descuento;
                $bonificacionesAplicadas[] = [
                    'tipo' => $bonificacion['tipo'],
                    'descuento_aplicado' => $descuento,
                    'porcentaje' => $bonificacion['descuento'] * 100
                ];
            }
        }
        
        return [
            'precio_base' => $precioBase,
            'precio_final' => round($precioFinal, 2),
            'bonificaciones_aplicadas' => $bonificacionesAplicadas,
            'advertencia_precios' => [
                'mensaje' => 'Los precios mostrados son orientativos y pueden sufrir cambios hasta que se realice el pago final. Los precios finales se confirmarán en el momento de la reserva.',
                'tipo' => 'advertencia',
                'importante' => true
            ]
        ];
    }
}
