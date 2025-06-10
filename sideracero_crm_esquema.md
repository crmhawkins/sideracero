
# 🧱 SIDERACERO CRM - Esquema General

## 🗂️ Módulos Principales

---

### 1. 🔧 Usuarios y Roles
- Modelo: `User`
- Roles posibles: `admin`, `comercial`, `cliente`
- Autenticación con Laravel Breeze o Jetstream
- Control de acceso con middleware o Spatie Permissions

---

### 2. 👥 Clientes
- Modelo: `Cliente`
- Campos:
  - `nombre_empresa`
  - `cif`
  - `persona_contacto`
  - `email`, `telefono`
  - `direccion`, `ciudad`, `provincia`, `codigo_postal`
  - `notas`

---

### 3. 📦 Productos
- Modelo: `Producto`
- Campos:
  - `referencia` (clave única)
  - `nombre`
  - `descripcion`
  - `categoria_id`
  - `precio_base`
  - `unidad` (ej: metros, kg, unidad)
  - `stock` (opcional)
  - `impuesto` (opcional)

---

### 4. 🗂️ Categorías de Productos
- Modelo: `CategoriaProducto`
- Campos:
  - `nombre`
  - `descripcion`

---

### 5. 📝 Presupuestos
- Modelo: `Presupuesto`
- Campos:
  - `cliente_id`
  - `fecha_emision`
  - `estado` (`borrador`, `enviado`, `aceptado`, `rechazado`)
  - `notas`
- Relación: `PresupuestoItem`
  - `producto_id`, `cantidad`, `precio_unitario`, `subtotal`

---

### 6. 💳 Facturas
- Modelo: `Factura`
- Campos:
  - `numero_factura`
  - `cliente_id`
  - `fecha_pago`, `forma_pago`
  - `estado` (`pendiente`, `pagado`, `vencido`)
- Relación con `FacturaItem`

---

### 7. 📧 Email Entrante (IA + presupuestos)
- Modelo: `CorreoEntrante`
- Campos:
  - `remitente`, `asunto`, `cuerpo`, `adjunto_path`
  - `analizado` (boolean)
  - `productos_detectados` (json)
- Flujo:
  1. Se recibe correo
  2. Se analiza el adjunto con GPT-4o
  3. Se extraen productos
  4. Se genera presupuesto automáticamente

---

### 8. 💬 Chat / Asistente IA
- Modelo: `ConversacionIA`
  - `usuario_id`, `mensaje_usuario`, `respuesta_ia`, `contexto`, `fecha`
- Permite probar funcionalidades de la IA desde el panel

---

### 9. 📈 Dashboard
- Métricas clave:
  - Nº presupuestos este mes
  - Total facturado
  - Top clientes
  - Top productos
  - Correos pendientes

---

## 🔗 Relaciones entre Modelos

```
User → tiene muchos → Presupuestos
Cliente → tiene muchos → Presupuestos, Facturas
Presupuesto → tiene muchos → PresupuestoItem
Producto → pertenece a → CategoriaProducto
Factura → tiene muchos → FacturaItem
CorreoEntrante → puede generar → Presupuesto
```

---

## 🔧 Paquetes Laravel Sugeridos

- `laravel-excel/laravel-excel` → importar precios
- `spatie/laravel-permission` → roles
- `laravel/telescope` → debug
- `filament/filament` o Laravel Nova → admin UI

---

## 🚀 Pasos Iniciales

1. Crear proyecto Laravel + base de datos
2. Crear migraciones y modelos: Clientes, Productos, Presupuestos
3. CRUDs básicos con Blade o Filament
4. Importación de precios desde Excel
5. Lectura de correos
6. Integración OpenAI API
