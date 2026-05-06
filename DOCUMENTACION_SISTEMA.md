# Documentación del Sistema - Chuquis Coffee

## 1. Información General

- **Nombre del Sistema**: Chuquis Coffee
- **Framework**: Laravel 12 + Vue 3 (SPA con Inertia.js)
- **Base de Datos**: MySQL (`chuquis_coffee`)
- **Control de Permisos**: `spatie/laravel-permission`
- **URL Local**: `http://127.0.0.1:8000`
- **Sesión**: Database, 120 minutos de expiración

---

## 2. Usuarios por Defecto

| Rol | Email | Contraseña | Descripción |
|-----|-------|------------|-------------|
| Admin | admin@gmail.com | 12345678 | Acceso total al sistema |
| Gerente | gerente@gmail.com | 12345678 | Gestión operativa amplia |
| Ventas | ventas@gmail.com | 12345678 | Registro y gestión de ventas |
| Cajero | cajero@gmail.com | 12345678 | Operaciones de caja básicas |
| Contador | contador@gmail.com | 12345678 | Solo lectura de reportes y datos |
| Vistas | vistas@gmail.com | 12345678 | Acceso solo lectura general |

> **Importante**: Cambiar todas las contraseñas por defecto en producción.

---

## 3. Roles y Permisos

### 3.1 Permisos Definidos (28 total)

| Módulo | Crear | Ver | Editar | Borrar |
|--------|-------|-----|--------|--------|
| **Usuarios** | `crear-usuario` | `ver-usuario` | `editar-usuario` | `borrar-usuario` |
| **Proveedores** | `crear-proveedor` | `ver-proveedor` | `editar-proveedor` | `borrar-proveedor` |
| **Categorías** | `crear-categoria` | `ver-categoria` | `editar-categoria` | `borrar-categoria` |
| **Productos** | `crear-producto` | `ver-producto` | `editar-producto` | `borrar-producto` |
| **Clientes** | `crear-cliente` | `ver-cliente` | `editar-cliente` | `borrar-cliente` |
| **Ventas** | `crear-venta` | `ver-venta` | `editar-venta` | `borrar-venta` |
| **Pagos** | `crear-pago` | `ver-pago` | `editar-pago` | `borrar-pago` |
| **Tipos** | `crear-tipo` | `ver-tipo` | `editar-tipo` | `borrar-tipo` |
| **Roles** | `crear-rol` | `ver-rol` | `editar-rol` | `borrar-rol` |
| **Marcas** | `crear-marca` | `ver-marca` | `editar-marca` | `borrar-marca` |

### 3.2 Matriz de Acceso por Rol

| Permiso | Admin | Gerente | Ventas | Cajero | Contador | Vistas |
|---------|:-----:|:-------:|:------:|:------:|:--------:|:------:|
| **Usuarios** (crear/ver/editar/borrar) | ✅ | ❌ / ver / ❌ / ❌ | ❌ | ❌ | ❌ | ❌ / ver / ❌ / ❌ |
| **Proveedores** (crear/ver/editar/borrar) | ✅ | ❌ / ver / ❌ / ❌ | ❌ | ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Categorías** (crear/ver/editar/borrar) | ✅ | ✅ | ✅ | ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Productos** (crear/ver/editar/borrar) | ✅ | ✅ | ✅ | ver / ❌ / ❌ / ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Clientes** (crear/ver/editar/borrar) | ✅ | ✅ | ✅ | ❌ / ver / crear-editar / ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Ventas** (crear/ver/editar/borrar) | ✅ | ✅ | ✅ | ❌ / ver / crear-editar / ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Pagos** (crear/ver/editar/borrar) | ✅ | ✅ | ✅ | ❌ / ver / crear-editar / ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Tipos** (crear/ver/editar/borrar) | ✅ | ✅ | ✅ | ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |
| **Roles** (crear/ver/editar/borrar) | ✅ | ❌ / ver / ❌ / ❌ | ❌ | ❌ | ❌ | ❌ / ver / ❌ / ❌ |
| **Marcas** (crear/ver/editar/borrar) | ✅ | ❌ | ❌ | ❌ | ❌ / ver / ❌ / ❌ | ❌ / ver / ❌ / ❌ |

---

## 4. Rutas Web (Frontend SPA)

Las rutas son manejadas por Vue Router en el frontend. Todas requieren autenticación excepto las indicadas.

| Ruta | Componente | Acceso Mínimo Requerido |
|------|-----------|------------------------|
| `/` | Welcome.vue | Público (sin login) |
| `/login` | Login.vue | Público |
| `/register` | Register.vue | Público |
| `/forgot-password` | ForgotPassword.vue | Público |
| `/reset-password/:token` | ResetPassword.vue | Público |
| `/dashboard` | Dashboard.vue | Autenticado |
| `/admin` | AdminDashboard.vue | Solo Admin |
| `/profile` | ProfileEdit.vue | Autenticado |
| `/clientes` | clientes/Index.vue | `ver-cliente` |
| `/categorias` | categorias/Index.vue | `ver-categoria` |
| `/marcas` | marcas/Index.vue | `ver-marca` |
| `/tipos` | tipos/Index.vue | `ver-tipo` |
| `/productos` | productos/Index.vue | `ver-producto` |
| `/proveedores` | proveedores/Index.vue | `ver-proveedor` |
| `/proveedores-productos` | proveedores-producto/Index.vue | `ver-proveedor` + Admin |
| `/ventas` | ventas/Index.vue | `ver-venta` |
| `/pagos` | pagos/Index.vue | `ver-pago` |
| `/reportes` | reportes/Index.vue | Admin / Contador / Gerente |
| `/roles` | roles/Index.vue | `ver-rol` |
| `/users` | users/Index.vue | `ver-usuario` |

---

## 5. Endpoints API

Todos los endpoints requieren autenticación (`auth` middleware) + permiso específico.

### 5.1 Autenticación

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/login` | Iniciar sesión |
| POST | `/logout` | Cerrar sesión |
| POST | `/register` | Registro de usuario (throttle: 5/min) |
| POST | `/forgot-password` | Solicitar reset de contraseña |
| POST | `/reset-password` | Resetear contraseña |
| PUT | `/password` | Cambiar contraseña (autenticado) |
| GET | `/api/user` | Obtener datos del usuario con roles y permisos |
| GET/PUT/DELETE | `/api/profile` | Gestionar perfil del usuario |

### 5.2 Admin Dashboard

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/admin/dashboard` | `ver-usuario` | Métricas del panel admin |

### 5.3 Clientes

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/clientes` | `ver-cliente` | Listar clientes (paginado) |
| POST | `/api/clientes` | `crear-cliente` | Crear cliente |
| GET | `/api/clientes/{id}` | `ver-cliente` | Ver detalle |
| PUT | `/api/clientes/{id}` | `editar-cliente` | Actualizar |
| DELETE | `/api/clientes/{id}` | `borrar-cliente` | Eliminar |

### 5.4 Categorías

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/categorias` | `ver-categoria` | Listar |
| POST | `/api/categorias` | `crear-categoria` | Crear |
| PUT | `/api/categorias/{id}` | `editar-categoria` | Actualizar |
| DELETE | `/api/categorias/{id}` | `borrar-categoria` | Eliminar |

### 5.5 Marcas

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/marcas` | `ver-marca` | Listar |
| POST | `/api/marcas` | `crear-marca` | Crear |
| PUT | `/api/marcas/{id}` | `editar-marca` | Actualizar |
| DELETE | `/api/marcas/{id}` | `borrar-marca` | Eliminar |

### 5.6 Tipos

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/tipos` | `ver-tipo` | Listar |
| POST | `/api/tipos` | `crear-tipo` | Crear |
| PUT | `/api/tipos/{id}` | `editar-tipo` | Actualizar |
| DELETE | `/api/tipos/{id}` | `borrar-tipo` | Eliminar |

### 5.7 Productos

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/productos` | `ver-producto` | Listar |
| POST | `/api/productos` | `crear-producto` | Crear |
| PUT | `/api/productos/{id}` | `editar-producto` | Actualizar |
| DELETE | `/api/productos/{id}` | `borrar-producto` | Eliminar |

### 5.8 Proveedores

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/proveedores` | `ver-proveedor` | Listar |
| POST | `/api/proveedores` | `crear-proveedor` | Crear |
| PUT | `/api/proveedores/{id}` | `editar-proveedor` | Actualizar |
| DELETE | `/api/proveedores/{id}` | `borrar-proveedor` | Eliminar |

### 5.9 Proveedor-Producto

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/proveedores-productos` | `ver-proveedor` | Listar |
| POST | `/api/proveedores-productos` | `crear-proveedor` | Crear |
| PUT | `/api/proveedores-productos/{id}` | `editar-proveedor` | Actualizar |
| DELETE | `/api/proveedores-productos/{id}` | `borrar-proveedor` | Eliminar |

### 5.10 Ventas

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/ventas` | `ver-venta` | Listar ventas (paginado) |
| POST | `/api/ventas` | `crear-venta` | Registrar venta |
| GET | `/api/ventas/{id}` | `ver-venta` | Ver detalle |
| DELETE | `/api/ventas/{id}` | `borrar-venta` | Eliminar |
| GET | `/api/ventas/cliente/{ci}` | `ver-venta` | Ventas por cliente |
| GET | `/api/ventas/producto/{id}` | `ver-venta` | Ventas por producto |

**Payload para crear venta (POST `/api/ventas`):**
```json
{
  "cliente_ci": "12345678",
  "producto_id": 1,
  "cantidad": 2,
  "precio": 25.50,
  "tipo_pago": "efectivo",
  "monto_recibido": 60.00
}
```

**Para pago mixto:**
```json
{
  "cliente_ci": "12345678",
  "producto_id": 1,
  "cantidad": 2,
  "precio": 25.50,
  "tipo_pago": "mixto",
  "pagos_mixtos": [
    { "tipo_pago": "efectivo", "monto": 20.00 },
    { "tipo_pago": "qr", "monto": 31.00 }
  ]
}
```

### 5.11 Pagos

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/pagos` | `ver-pago` | Listar |
| POST | `/api/pagos` | `crear-pago` | Crear |
| PUT | `/api/pagos/{id}` | `editar-pago` | Actualizar |
| DELETE | `/api/pagos/{id}` | `borrar-pago` | Eliminar |

### 5.12 Pagos Mixtos

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/pagos-mixtos` | `ver-pago` | Listar |
| POST | `/api/pagos-mixtos` | `crear-pago` | Crear |
| PUT | `/api/pagos-mixtos/{id}` | `editar-pago` | Actualizar |
| DELETE | `/api/pagos-mixtos/{id}` | `borrar-pago` | Eliminar |

### 5.13 Reportes

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/reportes` | `ver-venta` | Reporte de ventas |

### 5.14 Roles

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/roles` | `ver-rol` | Listar roles |
| POST | `/api/roles` | `crear-rol` | Crear rol |
| PUT | `/api/roles/{id}` | `editar-rol` | Actualizar |
| DELETE | `/api/roles/{id}` | `borrar-rol` | Eliminar |

### 5.15 Usuarios

| Método | Ruta | Permiso | Descripción |
|--------|------|---------|-------------|
| GET | `/api/users` | `ver-usuario` | Listar usuarios |
| POST | `/api/users` | `crear-usuario` | Crear usuario |
| PUT | `/api/users/{id}` | `editar-usuario` | Actualizar |
| DELETE | `/api/users/{id}` | `borrar-usuario` | Eliminar |

---

## 6. Sidebar y Visibilidad por Permiso

La barra lateral (`AppLayout.vue`) muestra opciones según el rol y permisos del usuario:

| Sección | Visibilidad |
|---------|-------------|
| **Dashboard** | Todos los autenticados |
| **Operaciones → Clientes** | `ver-cliente` |
| **Operaciones → Ventas** | `ver-venta` |
| **Operaciones → Pagos** | `ver-pago` |
| **Catálogo → Productos** | Todos excepto Cajero/Contador, o `ver-producto` (Cajero solo ve enlace) |
| **Catálogo → Categorías** | `ver-categoria` (no Cajero/Contador) |
| **Catálogo → Marcas** | `ver-marca` (no Cajero/Contador) |
| **Catálogo → Tipos** | `ver-tipo` (no Cajero/Contador) |
| **Proveedores → Proveedores** | `ver-proveedor` (no Cajero/Contador) |
| **Proveedores → Prov-Producto** | Solo Admin |
| **Administración → Panel de Administración** | Solo Admin |
| **Administración → Reportes** | Admin, Contador, Gerente |
| **Administración → Roles** | `ver-rol` |
| **Administración → Usuarios** | `ver-usuario` (no Cajero) |

---

## 7. Base de Datos - Tablas Relevantes

### 7.1 Control de Acceso

| Tabla | Descripción |
|-------|-------------|
| `users` | Cuentas de usuario |
| `roles` | Definición de roles (id, name, guard_name) |
| `permissions` | Definición de permisos (id, name, guard_name) |
| `model_has_roles` | Asignación usuario → rol |
| `model_has_permissions` | Permisos directos a usuario |
| `role_has_permissions` | Asignación rol → permiso |
| `password_reset_tokens` | Tokens de reset de contraseña |
| `sessions` | Almacenamiento de sesiones |

### 7.2 Módulos del Sistema

| Tabla | Descripción |
|-------|-------------|
| `clientes` | Clientes del sistema (PK: ci) |
| `productos` | Productos con stock (FK: marca_id, categoria_id, tipo_id) |
| `marcas` | Marcas de productos |
| `categorias` | Categorías de productos |
| `tipos` | Tipos de productos |
| `proveedores` | Proveedores |
| `proveedor_producto` | Relación proveedor-producto |
| `ventas` | Cabecera de ventas (FK: cliente_id, pago_id) |
| `venta_productos` | Detalle de venta (productos vendidos) |
| `pagos` | Pagos (con soporte para pagos mixtos via `pago_mixto_id`) |
| `pagos_mixto` | Tabla de pagos mixtos |

---

## 8. Autenticación y Flujo de Sesión

1. **Login**: `POST /login` con `{ email, password }`
2. **Respuesta**: Sesión creada + usuario con `roles.permissions` cargados
3. **Verificación**: `GET /api/user` retorna usuario, roles y permisos
4. **Sidebar**: Se renderiza según permisos del usuario
5. **Logout**: `POST /logout` destruye la sesión

**Middleware utilizado:**
- `auth`: Verifica sesión activa
- `permission:xxx`: Verifica permiso específico (Spatie)
- `guest`: Solo usuarios no autenticados
- `throttle`: Limita intentos de registro

---

## 9. Configuración de Entorno

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chuquis_coffee
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
APP_DEBUG=true
APP_ENV=local
APP_URL=http://127.0.0.1:8000
```

---

## 10. Comandos Útiles

| Comando | Descripción |
|---------|-------------|
| `php artisan serve` | Iniciar servidor de desarrollo |
| `npm run dev` | Iniciar Vite en modo desarrollo |
| `npm run build` | Construir assets para producción |
| `php artisan migrate` | Ejecutar migraciones pendientes |
| `php artisan db:seed` | Ejecutar seeders |
| `php artisan permission:cache-reset` | Limpiar caché de permisos |

---

## 11. Recomendaciones de Seguridad

1. **Cambiar contraseñas por defecto** - Todos los usuarios usan `12345678`
2. **Establecer `APP_DEBUG=false`** en producción
3. **Habilitar HTTPS** en producción
4. **Usar variables de entorno** para credenciales en seeders
5. **Configurar rate limiting** en endpoints críticos
6. **Auditar logs** de acceso regularmente
