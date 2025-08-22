# Herramientas de OpenAI para PortalFerry

## 🚢 Descripción

Este sistema integra herramientas de OpenAI que permiten a la IA acceder a información detallada sobre rutas marítimas, puertos, destinos y navieras para responder consultas de clientes de manera precisa y actualizada.

## 📋 Características

- ✅ **Filtrado inteligente** por origen, destino y naviera
- ✅ **Búsqueda por texto** en nombres de puertos y ciudades
- ✅ **Información completa** con duración, frecuencia y navieras
- ✅ **Integración automática** en chat y análisis de correos
- ✅ **Fácil mantenimiento** y actualización de datos

## 🛠️ Instalación y Configuración

### 1. Verificar archivos necesarios

Asegúrate de que existe el archivo de datos:
```bash
storage/app/openai/rutas_destinos.json
```

### 2. Verificar dependencias

El sistema requiere:
- Laravel 10+
- OpenAI API Key configurada en `.env`
- PHP 8.1+

### 3. Configurar variables de entorno

En tu archivo `.env`:
```env
OPENAI_API_KEY=tu_api_key_aqui
```

## 🧪 Pruebas

### Comando de prueba manual

```bash
# Probar todas las rutas
php artisan openai:test-tools

# Probar rutas desde Algeciras
php artisan openai:test-tools --origen=ALG

# Probar rutas de Baleària
php artisan openai:test-tools --naviera=Baleària

# Probar rutas a Ceuta
php artisan openai:test-tools --destino=CEU

# Buscar por texto
php artisan openai:test-tools --buscar=barcelona

# Probar precios
php artisan openai:test-tools --precios

# Probar precios específicos
php artisan openai:test-tools --precios --origen=ALG --destino=CEU

# Probar precio específico por tipo
php artisan openai:test-tools --precios --origen=ALG --destino=CEU --tipo=pasajero_ida

# Combinar filtros
php artisan openai:test-tools --origen=ALG --destino=CEU --naviera=FRS
```

### Tests automatizados

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar solo tests de herramientas OpenAI
php artisan test --filter=OpenAiToolsTest
```

## 📊 Estructura de Datos

### Códigos de puertos principales:

| Código | Puerto |
|--------|--------|
| ALG | Algeciras |
| BCN | Barcelona |
| VLC | Valencia |
| LEI | Almería |
| CDI | Cádiz |
| CEU | Ceuta |
| PMI | Mallorca |
| IBZ | Ibiza |
| LPA | Gran Canaria |
| TCI | Tenerife |
| ACE | Arrecife (Lanzarote) |

### Navieras disponibles:

- **FRS**: Ferries del Estrecho
- **Baleària**: Baleària
- **Armas/Trasmediterranea**: Armas/Trasmediterranea
- **Inter Shipping**: Inter Shipping

## 🔧 Uso en el Sistema

### 1. Chat en tiempo real

Los usuarios pueden hacer preguntas como:
- "¿Qué rutas hay desde Barcelona?"
- "¿Cuánto tarda el ferry de Algeciras a Ceuta?"
- "¿Qué navieras operan en las Islas Baleares?"
- "¿Hay rutas a Marruecos?"
- "¿Cuánto cuesta un billete de Algeciras a Ceuta?"
- "¿Cuál es el precio para un coche en la ruta Barcelona-Mallorca?"
- "¿Hay descuentos para residentes en Ceuta?"

### 2. Análisis automático de correos

El comando `correos:analizar` utiliza las mismas herramientas para:
- Categorizar correos entrantes
- Detectar consultas sobre rutas
- Generar respuestas automáticas

```bash
php artisan correos:analizar
```

## 📝 Ejemplos de Uso

### Consulta general de rutas
```json
{
  "origen": "todos",
  "destino": "todos",
  "naviera": "todas"
}
```

### Rutas específicas desde Algeciras
```json
{
  "origen": "ALG"
}
```

### Rutas de Baleària a las Islas Baleares
```json
{
  "origen": "BCN",
  "naviera": "Baleària"
}
```

### Precios para una ruta específica
```json
{
  "origen": "ALG",
  "destino": "CEU"
}
```

### Precio específico para pasajero
```json
{
  "origen": "ALG",
  "destino": "CEU",
  "tipo": "pasajero_ida"
}
```

## 🔄 Mantenimiento

### Actualizar datos de rutas

1. Editar el archivo `storage/app/openai/rutas_destinos.json`
2. Los cambios se reflejan automáticamente
3. No requiere reinicio del sistema

### Agregar nuevas navieras

1. Agregar la naviera al array `navieras` en el JSON
2. Incluir la naviera en las rutas correspondientes
3. Verificar con el comando de prueba

### Agregar nuevos puertos

1. Agregar el puerto a `puertos_origen`
2. Agregar el puerto a `destinos_disponibles` si es destino
3. Crear las rutas correspondientes en `destinos_por_origen`

## 🚨 Solución de Problemas

### Error: "No se pudo cargar la información de rutas"

1. Verificar que existe `storage/app/openai/rutas_destinos.json`
2. Verificar permisos de lectura del archivo
3. Verificar que el JSON es válido

### Error: "Tool no reconocida"

1. Verificar que el nombre de la herramienta coincide
2. Verificar que los parámetros son correctos
3. Revisar logs de Laravel

### Error de OpenAI API

1. Verificar que `OPENAI_API_KEY` está configurada
2. Verificar que la API key es válida
3. Verificar límites de uso de la API

## 📈 Monitoreo

### Logs importantes

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Ver logs específicos de OpenAI
grep "OpenAI" storage/logs/laravel.log
```

### Métricas útiles

- Número de consultas por día
- Tiempo de respuesta promedio
- Errores de API
- Uso de herramientas por tipo

## 🤝 Contribución

Para contribuir al desarrollo:

1. Crear una rama para tu feature
2. Implementar los cambios
3. Agregar tests correspondientes
4. Actualizar documentación
5. Crear pull request

## 📞 Soporte

Para soporte técnico:
- Revisar la documentación en `docs/openai_tools_rutas_destinos.md`
- Ejecutar tests para verificar funcionamiento
- Revisar logs para identificar errores
- Contactar al equipo de desarrollo

