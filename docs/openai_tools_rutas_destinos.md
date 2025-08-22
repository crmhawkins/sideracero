# Herramientas de OpenAI para Rutas y Destinos

## Descripción

El sistema de PortalFerry incluye herramientas de OpenAI que permiten a la IA acceder a información detallada sobre rutas marítimas, puertos, destinos y navieras para responder consultas de clientes de manera precisa.

## Herramientas Disponibles

### 1. obtener_rutas_destinos

Esta herramienta permite obtener información completa sobre rutas marítimas con filtros opcionales.

#### Parámetros:
- `origen` (string, opcional): Código del puerto de origen (ej: ALG, BCN, VLC) o 'todos'
- `destino` (string, opcional): Código del puerto de destino (ej: CEU, PMI, TCI) o 'todos'
- `naviera` (string, opcional): Nombre de la naviera (ej: Baleària, FRS, Armas/Trasmediterranea) o 'todas'

#### Ejemplos de uso:

**Obtener todas las rutas:**
```json
{
  "origen": "todos",
  "destino": "todos",
  "naviera": "todas"
}
```

**Rutas desde Algeciras:**
```json
{
  "origen": "ALG"
}
```

**Rutas de Baleària desde Barcelona:**
```json
{
  "origen": "BCN",
  "naviera": "Baleària"
}
```

**Rutas específicas a Ceuta:**
```json
{
  "destino": "CEU"
}
```

### 2. obtener_precios_tarifas

Esta herramienta devuelve información sobre precios y tarifas de billetes de ferry con advertencia de que los precios pueden cambiar hasta el pago.

#### Parámetros:
- `origen` (string, opcional): Código del puerto de origen (ej: ALG, BCN, VLC)
- `destino` (string, opcional): Código del puerto de destino (ej: CEU, PMI, TCI)
- `tipo` (string, opcional): Tipo de tarifa (ej: pasajero_ida, vehiculo_pequeño_ida, mascota)

#### Ejemplos de uso:

**Obtener todas las tarifas:**
```json
{}
```

**Precios para una ruta específica:**
```json
{
  "origen": "ALG",
  "destino": "CEU"
}
```

**Precio específico para pasajero:**
```json
{
  "origen": "ALG",
  "destino": "CEU",
  "tipo": "pasajero_ida"
}
```

**Advertencia incluida:**
Todas las respuestas incluyen una advertencia sobre que los precios son orientativos y pueden cambiar hasta el pago final.

## Información Disponible

### Puertos de Origen Principales:
- **ALG**: Algeciras
- **BCN**: Barcelona  
- **VLC**: Valencia
- **LEI**: Almería
- **CDI**: Cádiz
- **CEU**: Ceuta
- **PMI**: Mallorca
- **IBZ**: Ibiza
- **LPA**: Gran Canaria (Las Palmas)
- **TCI**: Tenerife (Santa Cruz)
- **ACE**: Arrecife (Lanzarote)

### Destinos Principales:
- **CEU**: Ceuta
- **TGM**: Tánger Med
- **PMI**: Mallorca
- **IBZ**: Ibiza
- **LPA**: Gran Canaria
- **TCI**: Tenerife
- **MLN**: Melilla
- **FOR**: Formentera

### Navieras Principales:
- **FRS**: Ferries del Estrecho
- **Baleària**: Baleària
- **Armas/Trasmediterranea**: Armas/Trasmediterranea
- **Inter Shipping**: Inter Shipping

## Casos de Uso

### 1. Consultas Generales de Rutas
Cuando un cliente pregunta sobre rutas disponibles, la IA puede usar:
```json
{
  "origen": "todos",
  "destino": "todos"
}
```

### 2. Consultas Específicas por Origen
Para preguntas como "¿Qué rutas hay desde Barcelona?":
```json
{
  "origen": "BCN"
}
```

### 3. Consultas por Naviera
Para preguntas como "¿Qué rutas opera Baleària?":
```json
{
  "naviera": "Baleària"
}
```

### 4. Consultas de Tiempo de Viaje
La información incluye duración y frecuencia de cada ruta.

## Estructura de Respuesta

La herramienta devuelve un JSON con la siguiente estructura:

```json
{
  "destinos_por_origen": {
    "ALG": {
      "origen_nombre": "ALGECIRAS",
      "destinos_disponibles": [
        {
          "codigo": "CEU",
          "nombre": "CEUTA",
          "navieras": ["FRS", "Baleària"],
          "duracion": "35-45 minutos",
          "frecuencia": "Múltiples salidas diarias"
        }
      ]
    }
  },
  "rutas_principales": [...],
  "destinos_disponibles": [...],
  "puertos_origen": [...],
  "navieras": [...]
}
```

### Estructura de respuesta para precios:

```json
{
  "advertencia_precios": {
    "mensaje": "Los precios mostrados son orientativos y pueden sufrir cambios hasta que se realice el pago final. Los precios finales se confirmarán en el momento de la reserva.",
    "tipo": "advertencia",
    "importante": true
  },
  "ruta": {
    "ruta_nombre": "ALGECIRAS-CEUTA",
    "pasajero_ida": 25.50,
    "pasajero_ida_vuelta": 45.00,
    "vehiculo_pequeño_ida": 35.00,
    "navieras": ["FRS", "Baleària"]
  },
  "precio_especifico": {
    "tipo": "pasajero_ida",
    "precio": 25.50,
    "moneda": "EUR"
  }
}
```

## Implementación Técnica

### Archivos Involucrados:
- `app/Services/OpenAiToolsService.php`: Servicio principal para procesar consultas
- `app/Http/Controllers/ChatGptController.php`: Controlador del chat
- `app/Console/Commands/AnalizarCorreos.php`: Comando para análisis de correos
- `storage/app/openai/rutas_destinos.json`: Datos de rutas y destinos

### Flujo de Funcionamiento:
1. El usuario hace una pregunta sobre rutas
2. ChatGPT detecta que necesita información de rutas
3. Se llama a la herramienta `obtener_rutas_destinos` con parámetros apropiados
4. El servicio filtra y devuelve la información relevante
5. ChatGPT genera una respuesta basada en los datos obtenidos

## Ventajas

1. **Respuestas Precisas**: La IA tiene acceso a datos actualizados y específicos
2. **Filtrado Inteligente**: Puede responder consultas específicas sin devolver toda la información
3. **Información Completa**: Incluye duración, frecuencia, navieras y más detalles
4. **Escalabilidad**: Fácil de mantener y actualizar con nuevos datos
5. **Consistencia**: Misma información en chat y análisis de correos

## Mantenimiento

Para actualizar la información de rutas:
1. Modificar el archivo `storage/app/openai/rutas_destinos.json`
2. Los cambios se reflejan automáticamente en todas las consultas
3. No requiere reinicio del sistema
