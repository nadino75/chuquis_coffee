# Documentación Técnica - Chuquis Coffee

> Sistema de Gestión para Cafetería — Laravel 12 + Vue 3 SPA

---

## Tabla de Contenidos

1. [Visión General del Sistema](#1-visión-general-del-sistema)
2. [Arquitectura](#2-arquitectura)
3. [Estructura del Proyecto](#3-estructura-del-proyecto)
4. [Stack Tecnológico](#4-stack-tecnológico)
5. [Base de Datos](#5-base-de-datos)
6. [Modelos Eloquent](#6-modelos-eloquent)
7. [Controladores](#7-controladores)
8. [API REST](#8-api-rest)
9. [Ruteo y Middleware](#9-ruteo-y-middleware)
10. [Frontend (Vue 3 SPA)](#10-frontend-vue-3-spa)
11. [Sistema de Permisos (RBAC)](#11-sistema-de-permisos-rbac)
12. [Autenticación y Sesiones](#12-autenticación-y-sesiones)
13. [Flujo de Datos: Venta Completa](#13-flujo-de-datos-venta-completa)
14. [Flujo de Autenticación](#14-flujo-de-autenticación)
15. [Build y Despliegue](#15-build-y-despliegue)
16. [Configuración de Entorno](#16-configuración-de-entorno)
17. [Seeders y Datos de Prueba](#17-seeders-y-datos-de-prueba)
18. [Testing](#18-testing)
19. [Seguridad](#19-seguridad)
20. [Consideraciones de Performance](#20-consideraciones-de-performance)

---

## 1. Visión General del Sistema

Chuquis Coffee es un sistema de gestión integral para una cafetería, implementado como una **Single Page Application (SPA)** con backend en **Laravel 12** y frontend en **Vue 3**. El sistema gestiona inventario, ventas, pagos (incluyendo pagos mixtos divididos en múltiples métodos), clientes, proveedores, reportes y control de acceso basado en roles (RBAC).

### Módulos Principales

| Módulo | Descripción |
|--------|-------------|
| **Dashboard** | Panel principal con métricas adaptadas por rol (General, Cajero, Contador, Admin) |
| **Clientes** | CRUD completo con búsqueda por CI, datos fiscales (NIT) |
| **Catálogo** | Gestión de Productos, Categorías, Marcas y Tipos |
| **Proveedores** | Registro de proveedores y asignación a productos |
| **Ventas** | Registro de ventas con cálculo automático de totales |
| **Pagos** | Soporte para efectivo, tarjeta, transferencia, QR y pagos mixtos |
| **Reportes** | Generación de reportes con exportación a PDF |
| **Administración** | Gestión de usuarios, roles, permisos y panel admin |

---

## 2. Arquitectura

### Patrón: Monolito con Frontend SPA

```
┌─────────────────────────────────────────────────────┐
│                    Navegador Web                     │
│  ┌───────────────────────────────────────────────┐  │
│  │              Vue 3 SPA (Router)               │  │
│  │  ┌─────────┐ ┌──────────┐ ┌────────────────┐ │  │
│  │  │ Layouts │ │  Views   │ │   Services     │ │  │
│  │  │ (Admin) │ │ (CRUD)   │ │  (Axios/API)   │ │  │
│  │  └────┬────┘ └────┬─────┘ └───────┬────────┘ │  │
│  └───────┼───────────┼───────────────┼──────────┘  │
└──────────┼───────────┼───────────────┼─────────────┘
           │           │               │
           ▼           ▼               ▼
┌─────────────────────────────────────────────────────┐
│              Laravel 12 (Backend)                    │
│  ┌──────────┐  ┌───────────┐  ┌──────────────────┐ │
│  │  Routes  │  │Middleware │  │   Controllers    │ │
│  │ web+API  │→ │auth+perm. │→ │  (Web + API)     │ │
│  └──────────┘  └───────────┘  └────────┬─────────┘ │
│                                         │           │
│  ┌──────────┐  ┌───────────┐  ┌────────▼─────────┐ │
│  │  Models  │← │  Services │← │   Requests       │ │
│  │ Eloquent │  │  Traits   │  │   (Validation)   │ │
│  └────┬─────┘  └───────────┘  └──────────────────┘ │
└───────┼─────────────────────────────────────────────┘
        │
        ▼
┌───────────────────┐
│   MySQL Database  │
│ (chuquis_coffee)  │
└───────────────────┘
```

### Flujo de Request

1. **Navegador** → `/{any}` route (excluye `/api`, `/storage`, `/favicon.ico`)
2. **Laravel** retorna `app.blade.php` (único archivo Blade)
3. **Blade** carga `app.js` compilado por Vite
4. **Vue Router** toma el control y maneja la navegación client-side
5. **Componentes Vue** hacen peticiones a `/api/*` via Axios
6. **Laravel API** procesa la request con middleware de autenticación y permisos
7. **Controladores** interactúan con modelos Eloquent → Base de datos
8. **Respuesta JSON** retorna al frontend

### Separación de Responsabilidades

| Capa | Tecnología | Responsabilidad |
|------|-----------|-----------------|
| **Presentación** | Vue 3 + AdminLTE 3 | UI, routing, estado reactivo |
| **API/Control** | Laravel Routes + Controllers | Routing, validación, orquestación |
| **Dominio** | Modelos Eloquent | Regla de negocio, relaciones |
| **Datos** | MySQL + Migrations | Persistencia, integridad |
| **Seguridad** | Spatie Permission | RBAC, autorización |

---

## 3. Estructura del Proyecto

```
chuquis_coffee-1/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminDashboardController.php   # Métricas admin
│   │   │   ├── DashboardController.php        # Dashboard por rol
│   │   │   ├── ProfileController.php          # Gestión de perfil
│   │   │   ├── CategoriaController.php        # CRUD categorías
│   │   │   ├── ClienteController.php          # CRUD clientes
│   │   │   ├── MarcaController.php            # CRUD marcas
│   │   │   ├── TipoController.php             # CRUD tipos
│   │   │   ├── ProductoController.php         # CRUD productos (con imagen)
│   │   │   ├── ProveedoreController.php       # CRUD proveedores
│   │   │   ├── ProveedoresProductoController.php  # Relación prov-prod
│   │   │   ├── VentaController.php            # CRUD ventas + API
│   │   │   ├── PagoController.php             # CRUD pagos
│   │   │   ├── PagosMixtoController.php       # CRUD pagos mixtos
│   │   │   ├── ReporteController.php          # Reportes + PDF
│   │   │   ├── RoleController.php             # CRUD roles
│   │   │   ├── UserController.php             # CRUD usuarios
│   │   │   └── Auth/                          # Auth controllers (Breeze)
│   │   │       ├── AuthenticatedSessionController.php
│   │   │       ├── RegisteredUserController.php
│   │   │       ├── PasswordResetLinkController.php
│   │   │       ├── NewPasswordController.php
│   │   │       ├── PasswordController.php
│   │   │       ├── EmailVerificationNotificationController.php
│   │   │       └── VerifyEmailController.php
│   │   ├── Requests/                          # Form Request validation
│   │   │   ├── Auth/LoginRequest.php
│   │   │   ├── CategoriaRequest.php
│   │   │   ├── ClienteRequest.php
│   │   │   ├── MarcaRequest.php
│   │   │   ├── PagoRequest.php
│   │   │   ├── PagosMixtoRequest.php
│   │   │   ├── ProductoRequest.php
│   │   │   ├── ProfileUpdateRequest.php
│   │   │   ├── ProveedoreRequest.php
│   │   │   ├── ProveedoresProductoRequest.php
│   │   │   └── TipoRequest.php
│   │   └── Traits/
│   │       └── ApiResourceTrait.php           # Helpers para respuestas API
│   ├── Models/
│   │   ├── User.php                           # Modelo usuario con HasRoles
│   │   ├── Cliente.php                        # PK string: ci
│   │   ├── Producto.php                       # Con scopes de stock
│   │   ├── Categoria.php                      # Auto-referencial
│   │   ├── Marca.php                          # Marcas de productos
│   │   ├── Tipo.php                           # Tipos de productos
│   │   ├── Proveedore.php                     # Proveedores
│   │   ├── ProveedoresProducto.php            # Pivot con datos extra
│   │   ├── Venta.php                          # Cabecera de venta
│   │   ├── VentaDetalle.php                   # Detalle (venta_productos)
│   │   ├── Pago.php                           # Pagos (auto-ref para mixtos)
│   │   ├── PagosMixto.php                     # Pagos mixtos
│   │   └── Reporte.php                        # Con SoftDeletes
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── View/Components/
│       ├── AppLayout.php                      # Blade layout component
│       └── GuestLayout.php                    # Layout para guests
├── bootstrap/
│   ├── app.php                                # Configuración app + middleware
│   └── providers.php                          # Service providers
├── config/
│   ├── adminlte.php                           # Configuración AdminLTE
│   ├── app.php                                # App config
│   ├── auth.php                               # Auth guards + providers
│   ├── cache.php                              # Cache config
│   ├── crud.php                               # CRUD generator config
│   ├── database.php                           # DB connections
│   ├── filesystems.php                        # Storage disks
│   ├── jwt.php                                # JWT config
│   ├── logging.php                            # Logging
│   ├── mail.php                               # Mail config
│   ├── permission.php                         # Spatie permission config
│   ├── queue.php                              # Queue config
│   ├── services.php                           # Third-party services
│   └── session.php                            # Session config
├── database/
│   ├── factories/UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_10_24_195625_create_proveedores_table.php
│   │   ├── 2025_10_24_195636_create_marcas_table.php
│   │   ├── 2025_10_24_195645_create_tipos_table.php
│   │   ├── 2025_10_24_195655_create_categorias_table.php
│   │   ├── 2025_10_24_195702_create_productos_table.php
│   │   ├── 2025_10_24_195709_create_proveedores_productos_table.php
│   │   ├── 2025_10_24_195715_create_clientes_table.php
│   │   ├── 2025_10_24_195724_create_pagos_table.php
│   │   ├── 2025_10_24_195749_create_ventas_table.php
│   │   ├── 2025_10_24_195849_create_venta_productos_table.php
│   │   ├── 2025_11_19_231457_create_permission_tables.php
│   │   ├── 2025_11_20_234501_create_reportes_table.php
│   │   ├── 2026_05_05_000001_add_marca_id_to_productos_table.php
│   │   ├── 2026_05_05_000002_create_pagos_mixto_table.php
│   │   └── 2026_05_05_000003_add_monto_recibido_and_cambio_to_pagos_table.php
│   └── seeders/                               # 19 seeders
├── resources/
│   ├── js/
│   │   ├── app.js                             # Entry point Vue + Router
│   │   ├── bootstrap.js                       # Axios + CSRF config
│   │   ├── App.vue                            # Root component (dynamic layout)
│   │   ├── routes/index.js                    # Vue Router definitions (20 routes)
│   │   ├── services/                          # API service layer (18 files)
│   │   │   ├── api.js                         # Axios instance + interceptors
│   │   │   ├── http.js                        # HTTP wrapper (get, post, put...)
│   │   │   ├── auth.js                        # Auth API calls
│   │   │   ├── authState.js                   # Reactive auth state manager
│   │   │   ├── dashboard.js
│   │   │   ├── productos.js
│   │   │   ├── clientes.js
│   │   │   ├── ventas.js
│   │   │   ├── pagos.js
│   │   │   ├── reportes.js
│   │   │   ├── users.js
│   │   │   ├── roles.js
│   │   │   ├── categorias.js
│   │   │   ├── marcas.js
│   │   │   ├── tipos.js
│   │   │   ├── proveedores.js
│   │   │   ├── proveedoresProducto.js
│   │   │   └── pagosMixto.js
│   │   ├── layouts/
│   │   │   ├── AppLayout.vue                  # AdminLTE sidebar + header
│   │   │   ├── AuthLayout.vue                 # Auth pages layout
│   │   │   └── DefaultLayout.vue              # Public pages layout
│   │   └── views/
│   │       ├── admin/AdminDashboard.vue
│   │       ├── auth/{Login,Register,ForgotPassword,ResetPassword}.vue
│   │       ├── dashboard/Dashboard.vue
│   │       ├── profile/ProfileEdit.vue
│   │       ├── public/{Welcome,NotFound}.vue
│   │       └── {clientes,categorias,marcas,tipos,productos,
│   │            proveedores,proveedores-producto,ventas,pagos,
│   │            pagos-mixto,reportes,users,roles}/Index.vue
│   └── views/
│       └── app.blade.php                      # SPA entry point
├── routes/
│   ├── web.php                                # Web routes + SPA catch-all
│   ├── api.php                                # API routes (permission-guarded)
│   ├── auth.php                               # Auth route definitions
│   └── console.php                            # Artisan commands
├── public/
│   ├── build/                                 # Vite production build output
│   └── index.php                              # Application entry point
├── storage/
│   ├── app/public/productos/                  # Uploaded product images
│   ├── framework/                             # Cache, sessions, views
│   └── logs/                                  # Laravel logs
├── tests/
│   ├── TestCase.php
│   ├── Unit/ExampleTest.php
│   └── Feature/
│       ├── ExampleTest.php
│       ├── ProfileTest.php
│       └── Auth/{6 test files}
├── composer.json                              # PHP dependencies
├── package.json                               # JS dependencies
├── vite.config.js                             # Vite configuration
├── tailwind.config.js                         # Tailwind config
├── postcss.config.js                          # PostCSS config
└── phpunit.xml                                # PHPUnit config
```

---

## 4. Stack Tecnológico

### Backend (PHP)

| Paquete | Versión | Propósito |
|---------|---------|-----------|
| `laravel/framework` | ^12.0 | Framework PHP principal |
| `spatie/laravel-permission` | ^6.23 | RBAC - Roles y Permisos |
| `barryvdh/laravel-dompdf` | ^3.1 | Generación de PDFs para reportes |
| `jeroennoten/laravel-adminlte` | ^3.15 | Integración AdminLTE 3 |
| `tymon/jwt-auth` | ^2.3 | Autenticación JWT (disponible, no usado activamente) |

**Requisitos**: PHP ^8.2, MySQL 5.7+ / MariaDB 10.3+

### Frontend (JavaScript)

| Paquete | Versión | Propósito |
|---------|---------|-----------|
| `vue` | ^3.5.33 | Framework de UI reactivo |
| `vue-router` | ^4.6.4 | Ruteo client-side |
| `axios` | ^1.16.0 | Cliente HTTP para API |
| `chart.js` | ^4.5.1 | Visualización de datos (gráficos) |
| `bootstrap` | ^5.3.3 | Framework CSS |
| `@popperjs/core` | ^2.11.8 | Tooltips, popovers |
| `alpine.js` | ^3.4.2 | JS ligero para interactividad |
| `tailwindcss` | ^3.1.0 | Utility-first CSS |
| `sass` | ^1.62.1 | Preprocesador CSS |
| `vite` | ^6.0.11 | Build tool + dev server |
| `@vitejs/plugin-vue` | ^6.0.6 | Plugin Vue para Vite |
| `laravel-vite-plugin` | ^1.2.0 | Integración Laravel + Vite |

### Herramientas de Desarrollo

| Herramienta | Propósito |
|------------|-----------|
| `laravel/breeze` | Scaffolding de autenticación |
| `laravel/pint` | Code style fixer (PHP) |
| `phpunit/phpunit` | Testing framework |
| `fakerphp/faker` | Generación de datos falsos |
| `ibex/crud-generator` | Generador de CRUDs |
| `concurrently` | Ejecutar múltiples procesos npm |

---

## 5. Base de Datos

### Esquema Completo

#### Tabla: `users`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `name` | VARCHAR(255) | No | - | Nombre completo |
| `email` | VARCHAR(255) (UNIQUE) | No | - | Email (usado para login) |
| `email_verified_at` | TIMESTAMP | Sí | NULL | Fecha de verificación |
| `password` | VARCHAR(255) | No | - | Hash bcrypt |
| `remember_token` | VARCHAR(100) | Sí | NULL | Token de sesión persistente |
| `created_at` | TIMESTAMP | Sí | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | Sí | NULL | Fecha de actualización |

#### Tabla: `clientes`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID interno |
| `ci` | VARCHAR(12) (UNIQUE) | No | - | Cédula de identidad (PK lógica) |
| `ci_complemento` | VARCHAR(3) | Sí | NULL | Complemento CI (ej: "1", "E") |
| `nit` | VARCHAR(20) | Sí | NULL | Número de identificación tributaria |
| `nombres` | VARCHAR(100) | No | - | Nombres del cliente |
| `apellido_paterno` | VARCHAR(100) | Sí | NULL | Apellido paterno |
| `apellido_materno` | VARCHAR(100) | Sí | NULL | Apellido materno |
| `sexo` | ENUM('masculino','femenino') | Sí | NULL | Género |
| `telefono` | VARCHAR(20) | Sí | NULL | Teléfono fijo |
| `celular` | VARCHAR(20) | Sí | NULL | Número celular |
| `correo` | VARCHAR(100) | No | - | Email |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `categorias`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `nombre` | VARCHAR(100) | No | - | Nombre de la categoría |
| `descripcion` | TEXT | Sí | NULL | Descripción |
| `tipo_id` | BIGINT UNSIGNED (FK → tipos.id) | No | - | Tipo al que pertenece |
| `categoria_id` | BIGINT UNSIGNED (FK → categorias.id) | Sí | NULL | Categoría padre (auto-ref) |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

**Relaciones**: Auto-referencial (categorías hijas), FK a `tipos`

#### Tabla: `marcas`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `nombre` | VARCHAR(255) | No | - | Nombre de la marca |
| `descripcion` | TEXT | Sí | NULL | Descripción |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `tipos`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `nombre` | VARCHAR(100) | No | - | Nombre del tipo |
| `descripcion` | TEXT | Sí | NULL | Descripción |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `productos`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `nombre` | VARCHAR(100) | No | - | Nombre del producto |
| `descripcion` | TEXT | No | - | Descripción detallada |
| `stock` | INT | No | 0 | Cantidad en inventario |
| `stock_minimo` | INT | No | 5 | Stock mínimo para alerta |
| `precio` | DECIMAL(8,2) | No | 0.00 | Precio de venta |
| `categoria_id` | BIGINT UNSIGNED (FK → categorias.id) | No | - | Categoría |
| `marca_id` | BIGINT UNSIGNED (FK → marcas.id) | Sí | NULL | Marca |
| `imagen` | VARCHAR(255) | Sí | NULL | Ruta de imagen |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `proveedores`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `nombre` | VARCHAR(255) | No | - | Nombre del proveedor |
| `direccion` | VARCHAR(255) | Sí | NULL | Dirección |
| `telefono` | VARCHAR(20) | Sí | NULL | Teléfono fijo |
| `celular` | VARCHAR(20) | Sí | NULL | Celular |
| `correo` | VARCHAR(100) | Sí | NULL | Email |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `proveedores_productos`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `proveedore_id` | BIGINT UNSIGNED (FK → proveedores.id) | No | - | Proveedor |
| `producto_id` | BIGINT UNSIGNED (FK → productos.id) | No | - | Producto |
| `cantidad` | DECIMAL(8,2) | No | 0.00 | Cantidad suministrada |
| `fecha_compra` | DATE | No | - | Fecha de compra |
| `fecha_vencimiento` | DATE | No | - | Fecha de vencimiento |
| `marca_id` | BIGINT UNSIGNED (FK → marcas.id) | No | - | Marca del producto |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `pagos`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `recibo` | VARCHAR(25) | No | - | Número de recibo (RC- + timestamp) |
| `fecha` | DATE | No | - | Fecha del pago |
| `tipo_pago` | ENUM('efectivo','qr','mixto','tarjeta','transferencia') | No | - | Método de pago |
| `total_pagado` | DECIMAL(8,2) | No | 0.00 | Monto total pagado |
| `monto_recibido` | DECIMAL(8,2) | Sí | NULL | Monto recibido (efectivo) |
| `cambio` | DECIMAL(8,2) | Sí | NULL | Cambio devuelto (efectivo) |
| `cliente_ci` | VARCHAR(12) (FK → clientes.ci) | No | - | Cliente asociado |
| `pago_mixto_id` | BIGINT UNSIGNED (FK → pagos.id) | Sí | NULL | ID del pago mixto padre |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

**Relación auto-referencial**: `pago_mixto_id` → `pagos.id` (SET NULL). Los sub-pagos de un pago mixto apuntan al pago principal.

#### Tabla: `pagos_mixto`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `recibo` | VARCHAR(50) | Sí | NULL | Número de recibo |
| `fecha` | DATE | Sí | NULL | Fecha |
| `tipo_pago` | ENUM('efectivo','tarjeta','transferencia','qr') | Sí | NULL | Método |
| `monto` | DECIMAL(10,2) | No | 0.00 | Monto del sub-pago |
| `pago_id` | BIGINT UNSIGNED (FK → pagos.id) | Sí | NULL | Pago principal |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `ventas`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `fecha_venta` | DATE | No | - | Fecha de la venta |
| `suma_total` | DECIMAL(8,2) | No | 0.00 | Total de la venta |
| `cliente_id` | BIGINT UNSIGNED (FK → clientes.id) | No | - | Cliente |
| `pago_id` | BIGINT UNSIGNED (FK → pagos.id) | No | - | Pago asociado |
| `detalles` | TEXT | Sí | NULL | Observaciones |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `venta_productos`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `id_producto` | BIGINT UNSIGNED (FK → productos.id) | No | - | Producto vendido |
| `id_venta` | BIGINT UNSIGNED (FK → ventas.id) | No | - | Venta asociada |
| `precio` | DECIMAL(8,2) | No | 0.00 | Precio unitario al momento de la venta |
| `cantidad` | INT | No | 1 | Cantidad vendida |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tabla: `reportes`

| Columna | Tipo | Nullable | Default | Descripción |
|---------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED (PK, AI) | No | - | ID único |
| `nombre` | VARCHAR(255) | No | - | Nombre del reporte |
| `descripcion` | TEXT | Sí | NULL | Descripción |
| `tipo` | ENUM('ventas','pagos','productos','inventario','clientes','general') | No | - | Tipo de reporte |
| `filtros` | JSON | Sí | NULL | Filtros aplicados |
| `datos` | JSON | Sí | NULL | Datos del reporte |
| `configuracion` | JSON | Sí | NULL | Configuración |
| `usuario_id` | BIGINT UNSIGNED (FK → users.id) | No | - | Usuario que generó |
| `deleted_at` | TIMESTAMP | Sí | NULL | Soft delete |
| `created_at` | TIMESTAMP | Sí | NULL | - |
| `updated_at` | TIMESTAMP | Sí | NULL | - |

#### Tablas Spatie Permission

| Tabla | Columnas | Descripción |
|-------|----------|-------------|
| `permissions` | id, name, guard_name, created_at, updated_at | Definición de permisos |
| `roles` | id, name, guard_name, created_at, updated_at | Definición de roles |
| `model_has_permissions` | permission_id, model_type, model_id | Permisos directos a modelo |
| `model_has_roles` | role_id, model_type, model_id | Roles asignados a modelo |
| `role_has_permissions` | permission_id, role_id | Permisos de cada rol |

### Diagrama de Relaciones (Texto)

```
users ─────(M:N)───── roles ←(M:N)→ permissions
                            ↑
                       model_has_roles

clientes ←──(1:N)── ventas ──(1:1)── pagos ──(1:N)── pagos (hijos, via pago_mixto_id)
    ↑                   ↑                   ↑
    │                   │                   │
  (1:N)             (1:N)              (1:1)
    │                   │                   │
  pagos           venta_productos ──(N:1)── productos
                                       ↑       ↑
                                  (N:1)│    (N:1)│
                                       │       │
                                   categorias  marcas
                                       ↑
                                    (N:1)
                                       │
                                     tipos

proveedores ──(1:N)── proveedores_productos ──(N:1)── productos
                                              └──(N:1)── marcas

reportes ──(N:1)── users
```

### Índices y Restricciones Clave

- `clientes.ci`: UNIQUE
- `users.email`: UNIQUE
- FK `ventas.cliente_id` → `clientes.id` (CASCADE)
- FK `ventas.pago_id` → `pagos.id` (CASCADE)
- FK `pagos.cliente_ci` → `clientes.ci` (CASCADE)
- FK `pagos.pago_mixto_id` → `pagos.id` (SET NULL)
- FK `venta_productos.id_producto` → `productos.id` (CASCADE)
- FK `venta_productos.id_venta` → `ventas.id` (CASCADE)
- FK `productos.categoria_id` → `categorias.id` (CASCADE)
- FK `productos.marca_id` → `marcas.id` (SET NULL)

---

## 6. Modelos Eloquent

### User (`app/Models/User.php`)

```php
use HasFactory, Notifiable, HasRoles;
$fillable = ['name', 'email', 'password'];
$hidden = ['password', 'remember_token'];
$casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];
```

**Traits**: `HasRoles` de Spatie permite `$user->assignRole()`, `$user->givePermissionTo()`, `$user->hasRole()`, etc.

### Producto (`app/Models/Producto.php`)

```php
$fillable = ['nombre', 'descripcion', 'precio', 'stock', 'stock_minimo', 'categoria_id', 'marca_id', 'imagen'];
$casts = ['precio' => 'decimal:2', 'stock' => 'integer', 'stock_minimo' => 'integer'];
```

**Relaciones**:
- `belongsTo(Categoria::class)` → categoría
- `belongsTo(Marca::class)` → marca
- `hasMany(VentaDetalle::class, 'id_producto')` → detalles de venta
- `belongsToMany(Venta::class, 'venta_productos')` → ventas (con pivot: precio, cantidad)

**Scopes**:
- `scopeConStock($q)` → `where('stock', '>', 0)`
- `scopeSinStock($q)` → `where('stock', '<=', 0)`
- `scopeStockBajo($q)` → `whereColumn('stock', '<', 'stock_minimo') && stock > 0`
- `scopeNecesitaReorden($q)` → `whereColumn('stock', '<=', 'stock_minimo')`

**Accessors**:
- `getStockBajoAttribute()` → boolean
- `getSinStockAttribute()` → boolean
- `getNivelStockAttribute()` → porcentaje (0-100%)
- `getClaseNivelStockAttribute()` → 'danger' | 'warning' | 'success'
- `getValorInventarioAttribute()` → `precio * stock`

**Métodos**:
- `reducirStock($cantidad)` → decrementa y valida
- `aumentarStock($cantidad)` → incrementa
- `static populares($limit = 10)` → productos con más ventas

### Cliente (`app/Models/Cliente.php`)

```php
$fillable = ['ci', 'ci_complemento', 'nit', 'nombres', 'apellido_paterno', 'apellido_materno', 'sexo', 'telefono', 'celular', 'correo'];
$casts = ['id' => 'integer', 'ci' => 'string', 'nit' => 'string'];
```

**Relaciones**:
- `hasMany(Venta::class, 'cliente_id')` → ventas del cliente
- `hasMany(Pago::class, 'cliente_ci', 'ci')` → pagos del cliente

### Venta (`app/Models/Venta.php`)

```php
$perPage = 20;
$fillable = ['fecha_venta', 'suma_total', 'cliente_id', 'pago_id', 'detalles'];
$casts = ['fecha_venta' => 'date', 'suma_total' => 'decimal:2'];
```

**Relaciones**:
- `belongsTo(Cliente::class, 'cliente_id')` → cliente
- `belongsTo(Pago::class, 'pago_id')` → pago
- `hasMany(VentaDetalle::class, 'id_venta')` → detalle
- `belongsToMany(Producto::class, 'venta_productos')` → productos vendidos

### Pago (`app/Models/Pago.php`)

```php
$fillable = ['recibo', 'fecha', 'tipo_pago', 'total_pagado', 'monto_recibido', 'cambio', 'cliente_ci', 'pago_mixto_id'];
$casts = ['fecha' => 'date', 'total_pagado' => 'decimal:2', 'monto_recibido' => 'decimal:2', 'cambio' => 'decimal:2'];
$with = ['pagosHijos']; // Carga eager automática de sub-pagos
```

**Relaciones**:
- `belongsTo(Cliente::class, 'cliente_ci', 'ci')` → cliente
- `belongsTo(Pago::class, 'pago_mixto_id', 'id')` → pago padre (mixto)
- `hasMany(Pago::class, 'pago_mixto_id', 'id')` → sub-pagos hijos
- `hasMany(Venta::class, 'pago_id', 'id')` → ventas asociadas

### VentaDetalle (`app/Models/VentaDetalle.php`)

```php
$table = 'venta_productos';
$fillable = ['id_producto', 'id_venta', 'precio', 'cantidad'];
$casts = ['precio' => 'decimal:2', 'cantidad' => 'integer'];
```

**Relaciones**:
- `belongsTo(Venta::class, 'id_venta')` → venta
- `belongsTo(Producto::class, 'id_producto')` → producto

### Categoria (`app/Models/Categoria.php`)

```php
$perPage = 20;
$fillable = ['nombre', 'descripcion', 'tipo_id', 'categoria_id'];
```

**Relaciones**:
- `belongsTo(Categoria::class, 'categoria_id')` → categoría padre
- `belongsTo(Tipo::class, 'tipo_id')` → tipo

### Marca (`app/Models/Marca.php`)

```php
$perPage = 20;
$fillable = ['nombre', 'descripcion'];
```

### Tipo (`app/Models/Tipo.php`)

```php
$perPage = 20;
$fillable = ['nombre', 'descripcion'];
```

### Proveedore (`app/Models/Proveedore.php`)

```php
$perPage = 20;
$fillable = ['nombre', 'contacto', 'telefono', 'celular', 'email', 'correo', 'direccion', 'ruc'];
```

### ProveedoresProducto (`app/Models/ProveedoresProducto.php`)

```php
$perPage = 20;
$fillable = ['proveedore_id', 'producto_id', 'cantidad', 'fecha_compra', 'fecha_vencimiento', 'marca_id'];
$casts = ['cantidad' => 'decimal:2', 'fecha_compra' => 'date', 'fecha_vencimiento' => 'date'];
```

**Relaciones**:
- `belongsTo(Proveedore::class, 'proveedore_id')`
- `belongsTo(Producto::class, 'producto_id')`
- `belongsTo(Marca::class, 'marca_id')`

### Reporte (`app/Models/Reporte.php`)

```php
use SoftDeletes;
$fillable = ['nombre', 'descripcion', 'tipo', 'filtros', 'datos', 'configuracion', 'usuario_id'];
$casts = ['filtros' => 'array', 'datos' => 'array', 'configuracion' => 'array'];
```

**Relaciones**:
- `belongsTo(User::class, 'usuario_id')` → usuario creador

### PagosMixto (`app/Models/PagosMixto.php`)

```php
$table = 'pagos_mixto';
$perPage = 20;
$fillable = ['recibo', 'fecha', 'tipo_pago', 'monto', 'pago_id'];
$casts = ['fecha' => 'date', 'monto' => 'decimal:2'];
```

**Relaciones**:
- `belongsTo(Pago::class, 'pago_id')` → pago principal

---

## 7. Controladores

### Patrón General

Cada controlador de recurso sigue un patrón consistente:

| Método Web | Método API | Permiso | Descripción |
|-----------|-----------|---------|-------------|
| `index()` | `indexApi()` | `ver-*` | Listar paginado |
| `create()` | — | `crear-*` | Formulario crear |
| `store()` | `storeApi()` | `crear-*` | Crear registro |
| `show()` | `showApi()` | `ver-*` | Ver detalle |
| `edit()` | — | `editar-*` | Formulario editar |
| `update()` | `updateApi()` | `editar-*` | Actualizar |
| `destroy()` | `destroyApi()` | `borrar-*` | Eliminar |

### VentaController (Detalle del Flujo de Venta)

**`storeApi()` — Registro de Venta**:

1. **Validación**:
   - `cliente_ci` → existe en `clientes.ci`
   - `producto_id` → existe en `productos.id`
   - `cantidad` → integer >= 1
   - `precio` → numeric >= 0
   - `tipo_pago` → enum: efectivo, tarjeta, transferencia, qr, mixto
   - `monto_recibido` → requerido si tipo_pago = efectivo
   - `pagos_mixtos` → requerido si tipo_pago = mixto

2. **Verificación de Stock**:
   - Busca producto por ID
   - Compara `stock` vs `cantidad` solicitada
   - Retorna 422 si stock insuficiente

3. **Cálculo de Total**:
   - `total = precio * cantidad`

4. **Validación de Pago**:
   - Si `efectivo`: verifica `monto_recibido >= total`, calcula `cambio = monto_recibido - total`
   - Si `mixto`: suma montos de sub-pagos, verifica que coincida con total (±0.01)

5. **Creación de Pago** (en DB transaction):
   - Genera recibo: `RC-` + hora (HHmmss) + random(100-999)
   - Crea registro en `pagos` con tipo_pago, total_pagado, y campos de efectivo si aplica

6. **Sub-pagos Mixtos** (si aplica):
   - Itera `pagos_mixtos` array
   - Crea cada sub-pago con `pago_mixto_id` apuntando al pago principal

7. **Creación de Venta**:
   - Busca cliente por CI
   - Crea registro en `ventas` con `pago_id` y `suma_total`

8. **Detalle de Venta**:
   - Crea registro en `venta_productos`

9. **Actualización de Stock**:
   - `producto.stock -= cantidad`

10. **Commit**: Retorna venta con relaciones cargadas (cliente, pago)

### DashboardController (Dashboard por Rol)

**`obtenerDatosDashboard()`**:

- Detecta rol del usuario autenticado
- Si `Cajero` → retorna: ventas por hora del día, distribución por método de pago hoy, productos disponibles, productos sin stock
- Si `Contador` → retorna: ingresos mes actual vs anterior, variación %, ticket promedio, ingresos por mes (últimos 6 meses), ingresos por método de pago, tendencia diaria
- Si otro → retorna: estadísticas generales, gráficos, alertas de sistema, ventas recientes

### AdminDashboardController

**`obtenerDatosAdmin()`**:

- `total_usuarios`, `total_roles`, `total_clientes`, `total_productos`
- `ventas_hoy` (count), `ingresos_totales` (sum)
- `alertasSistema()`: productos con stock bajo
- `ventasRecientes()`: últimas 10 ventas

### ReporteController

**Tipos de Reporte**: dashboard, ventas, pagos, productos, inventario, clientes

**Métodos clave**:
- `index()`: Genera reporte en tiempo real según tipo
- `obtenerDatosReporte()`: API endpoint para datos de reporte
- `descargarPDF()`: Genera y descarga PDF usando DomPDF
- `reporteVentas()`: Totales por día, método de pago, producto más vendido
- `reportePagos()`: Distribución por tipo, totales por período
- `reporteInventario()`: Valor total del inventario, productos con stock bajo
- `debug()`: Información de depuración del sistema

---

## 8. API REST

### Convenciones

- **Base URL**: `/api`
- **Autenticación**: Session cookies (con `withCredentials: true` en Axios)
- **Formato**: JSON (`Accept: application/json`, `Content-Type: application/json`)
- **CSRF**: Token enviado vía header `X-CSRF-TOKEN` y cookie `XSRF-TOKEN`
- **Respuestas exitosas**: 200/201 con datos JSON
- **Errores**: 422 (validación), 403 (permiso), 401 (no autenticado), 500 (error servidor)
- **Paginación**: Laravel paginator format (`{ data: [], current_page, last_page, total, per_page, from, to }`)

### Respuesta Estándar de Error (Validación)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "cliente_ci": ["The selected cliente ci is invalid."],
    "producto_id": ["The producto id field is required."]
  }
}
```

### Respuesta Estándar de Error (Permiso)

```json
{
  "message": "This action is unauthorized."
}
```

### Endpoints Completos

Ver sección 5 de la documentación de accesos para el listado completo de endpoints con permisos requeridos.

### Paginación

Todos los endpoints `GET /api/{recurso}` retornan datos paginados:

```json
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "...",
  "from": 1,
  "last_page": 5,
  "last_page_url": "...",
  "next_page_url": "...",
  "path": "...",
  "per_page": 10,
  "prev_page_url": null,
  "to": 10,
  "total": 50
}
```

### Parámetros de Query Soportados

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | int | Número de página |
| `per_page` | int | Registros por página (algunos endpoints) |
| `search` | string | Búsqueda por texto |

---

## 9. Ruteo y Middleware

### Ruteo Laravel

**Archivo de Bootstrap**: `bootstrap/app.php`

```php
Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
```

### Middleware Registrados

| Alias | Clase | Propósito |
|-------|-------|-----------|
| `auth` | Laravel builtin | Verifica sesión activa |
| `guest` | Laravel builtin | Solo usuarios NO autenticados |
| `signed` | Laravel builtin | Verifica firma de URL |
| `throttle` | Laravel builtin | Rate limiting |
| `verified` | Laravel builtin | Email verificado |
| `permission` | Spatie\Permission\Middleware\PermissionMiddleware | Verifica permiso específico |
| `role` | Spatie\Permission\Middleware\RoleMiddleware | Verifica rol específico |
| `role_or_permission` | Spatie\Permission\Middleware\RoleOrPermissionMiddleware | Verifica rol O permiso |

### Flujo de Ruteo Web

```
Request → routes/web.php
    ↓
SPA Catch-all: /{any} where any != api|storage|favicon.ico
    ↓
return view('app')  →  app.blade.php
    ↓
#app div + <script type="module" src="@vite('resources/js/app.js')">
    ↓
Vue Router toma el control
```

### SPA Entry (`resources/views/app.blade.php`)

El archivo Blade carga:
- Metadatos (charset, viewport, CSRF token)
- Título dinámico
- Asset compilado por Vite (`resources/js/app.js`)
- Elemento `<div id="app">` como mount point de Vue

---

## 10. Frontend (Vue 3 SPA)

### Arquitectura

```
app.js (Entry Point)
    ↓
createApp(App.vue)
    ↓
createRouter(routes)
    ↓
router.beforeEach() → auth guard
    ↓
app.mount('#app')
```

### Entry Point (`resources/js/app.js`)

```javascript
import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes';
import App from '@/App.vue';
import authState from './services/authState';

const app = createApp(App);

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() { return { top: 0 }; },
});

// Auth guard
router.beforeEach(async (to, from, next) => {
    const requiresAuth = to.matched.some(r => r.meta.requiresAuth);
    const requiresGuest = to.matched.some(r => r.meta.requiresGuest);

    if (requiresAuth && !authState.isAuthenticated()) {
        next({ name: 'login', query: { redirect: to.fullPath } });
    } else if (requiresGuest && authState.isAuthenticated()) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

// Dynamic title
router.afterEach((to) => {
    if (to.meta.title) {
        document.title = to.meta.title + ' - Chuquis Coffee';
    }
});

app.use(router);
app.mount('#app');
```

### Vue Router (`resources/js/routes/index.js`)

| Ruta | Nombre | Componente | Meta |
|------|--------|-----------|------|
| `/` | `welcome` | Welcome.vue | title: 'Inicio' |
| `/login` | `login` | Login.vue | layout: AuthLayout, requiresGuest: true |
| `/register` | `register` | Register.vue | layout: AuthLayout, requiresGuest: true |
| `/forgot-password` | `password.request` | ForgotPassword.vue | layout: AuthLayout, requiresGuest: true |
| `/reset-password/:token` | `password.reset` | ResetPassword.vue | layout: AuthLayout, requiresGuest: true |
| `/dashboard` | `dashboard` | Dashboard.vue | layout: AppLayout, requiresAuth: true |
| `/admin` | `admin.dashboard` | AdminDashboard.vue | layout: AppLayout, requiresAuth: true |
| `/profile` | `profile.edit` | ProfileEdit.vue | layout: AppLayout, requiresAuth: true |
| `/clientes` | `clientes.index` | clientes/Index.vue | layout: AppLayout, requiresAuth: true |
| `/categorias` | `categorias.index` | categorias/Index.vue | layout: AppLayout, requiresAuth: true |
| `/marcas` | `marcas.index` | marcas/Index.vue | layout: AppLayout, requiresAuth: true |
| `/tipos` | `tipos.index` | tipos/Index.vue | layout: AppLayout, requiresAuth: true |
| `/productos` | `productos.index` | productos/Index.vue | layout: AppLayout, requiresAuth: true |
| `/proveedores` | `proveedores.index` | proveedores/Index.vue | layout: AppLayout, requiresAuth: true |
| `/proveedores-productos` | `proveedores-productos.index` | proveedores-producto/Index.vue | layout: AppLayout, requiresAuth: true |
| `/ventas` | `ventas.index` | ventas/Index.vue | layout: AppLayout, requiresAuth: true |
| `/pagos` | `pagos.index` | pagos/Index.vue | layout: AppLayout, requiresAuth: true |
| `/pagos-mixto` | `pagos-mixto.index` | pagos-mixto/Index.vue | layout: AppLayout, requiresAuth: true |
| `/reportes` | `reportes.index` | reportes/Index.vue | layout: AppLayout, requiresAuth: true |
| `/roles` | `roles.index` | roles/Index.vue | layout: AppLayout, requiresAuth: true |
| `/users` | `users.index` | users/Index.vue | layout: AppLayout, requiresAuth: true |
| `/:catchAll(.*)` | `not-found` | NotFound.vue | title: '404 - No Encontrado' |

### Layout System

**Root Component (`App.vue`)**: Renderiza dinámicamente el layout especificado en `route.meta.layout`. Si no hay layout definido, usa un div simple.

```vue
<template>
    <component :is="layout">
        <RouterView />
    </component>
</template>

<script setup>
import { computed } from 'vue';
import { RouterView, useRouter } from 'vue-router';
import DefaultLayout from '@/layouts/DefaultLayout.vue';

const layout = computed(() => useRouter().currentRoute.value.meta.layout || DefaultLayout);
</script>
```

### Service Layer

**`api.js` — Axios Instance**:

```javascript
const api = axios.create({
    baseURL: window.location.origin,
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// CSRF token from meta tag
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    api.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

// 401 interceptor → redirect to login
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            authState.user = null;
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
```

**`authState.js` — Reactive State Manager**:

```javascript
const state = { user: window.__APP_DATA?.user || null };

export default {
    get user() { return state.user; },
    set user(val) { state.user = val; },
    get role() { return state.user?.roles?.[0]?.name || null; },
    hasRole(roleName) { return state.user?.roles?.some(r => r.name === roleName) ?? false; },
    hasAnyRole(roleNames) { return roleNames.some(name => this.hasRole(name)); },
    async login(credentials) { /* ... */ },
    async register(data) { /* ... */ },
    async logout() { /* ... */ },
    async refreshUser() { /* ... */ },
    isAuthenticated() { return state.user !== null; },
};
```

**Service Pattern**: Cada módulo tiene un servicio que envuelve las llamadas API:

```javascript
// Ejemplo: services/clientes.js
import http from './http';

export default {
    index(params = {}) { return http.get('/api/clientes', { params }); },
    show(ci) { return http.get(`/api/clientes/${ci}`); },
    store(data) { return http.post('/api/clientes', data); },
    update(ci, data) { return http.put(`/api/clientes/${ci}`, data); },
    destroy(ci) { return http.delete(`/api/clientes/${ci}`); },
};
```

### Componentes de Vista

**Patrón CRUD Index**:

Cada módulo (clientes, productos, ventas, etc.) sigue un patrón consistente:

1. **Tabla listada** con búsqueda y paginación
2. **Modal Create** con formulario
3. **Modal Edit** con formulario
4. **Modal Show** con detalle
5. **Delete confirmation** con `confirm()`

**Ejemplo de estructura de datos en componente**:

```javascript
const items = ref({ data: [], current_page: 1, last_page: 1, total: 0, per_page: 10 });
const loading = ref(false);
const success = ref('');
const error = ref('');
const search = ref('');
const showCreate = ref(false);
const showEdit = ref(false);
const showView = ref(false);
const currentItem = ref(null);
const createForm = reactive({ /* campos */ });
const editForm = reactive({ /* campos */ });
const formErrors = reactive({});
```

### AppLayout (Sidebar)

El sidebar (`AppLayout.vue`) renderiza menú basado en permisos:

```javascript
function can(permission) {
    // Admin siempre tiene todos los permisos
    if (user.value?.roles?.some(r => r.name === 'Admin')) return true;
    return user.value?.roles?.some(role =>
        role.permissions?.some(p => p.name === permission)
    );
}
```

Secciones del sidebar con visibilidad condicional:

| Sección | Items | Condición |
|---------|-------|-----------|
| **Dashboard** | Dashboard | Todos autenticados |
| **Operaciones** | Clientes, Ventas, Pagos | `ver-cliente`, `ver-venta`, `ver-pago` |
| **Catálogo** | Productos, Categorías, Marcas, Tipos | Oculto a Cajero/Contador |
| **Proveedores** | Proveedores, Prov-Producto | `ver-proveedor`, Prov-Producto solo Admin |
| **Administración** | Panel Admin, Reportes, Roles, Usuarios | Por rol/permiso |

---

## 11. Sistema de Permisos (RBAC)

### Implementación: Spatie Laravel Permission

**Configuración** (`config/permission.php`):
- Tablas: `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`
- Cache: 24 horas, key `spatie.permission.cache`
- Guard: `web`
- Teams: deshabilitado

### Permisos (28 total)

| Módulo | Crear | Ver | Editar | Borrar |
|--------|-------|-----|--------|--------|
| Usuarios | `crear-usuario` | `ver-usuario` | `editar-usuario` | `borrar-usuario` |
| Proveedores | `crear-proveedor` | `ver-proveedor` | `editar-proveedor` | `borrar-proveedor` |
| Categorías | `crear-categoria` | `ver-categoria` | `editar-categoria` | `borrar-categoria` |
| Productos | `crear-producto` | `ver-producto` | `editar-producto` | `borrar-producto` |
| Clientes | `crear-cliente` | `ver-cliente` | `editar-cliente` | `borrar-cliente` |
| Ventas | `crear-venta` | `ver-venta` | `editar-venta` | `borrar-venta` |
| Pagos | `crear-pago` | `ver-pago` | `editar-pago` | `borrar-pago` |
| Tipos | `crear-tipo` | `ver-tipo` | `editar-tipo` | `borrar-tipo` |
| Roles | `crear-rol` | `ver-rol` | `editar-rol` | `borrar-rol` |
| Marcas | `crear-marca` | `ver-marca` | `editar-marca` | `borrar-marca` |

### Roles

| Rol | Descripción | Alcance |
|-----|-------------|---------|
| **Admin** | Acceso total | Los 28 permisos |
| **Gerente** | Gestión operativa | Ventas, clientes, pagos, productos, categorías, tipos + lectura de usuarios, proveedores, roles |
| **Ventas** | Operaciones de venta | Ventas, clientes, pagos, productos, categorías, tipos (CRUD completo) |
| **Cajero** | Operaciones de caja | Ver productos/clientes, crear/editar clientes/ventas/pagos |
| **Contador** | Solo lectura financiera | Ver ventas, pagos, clientes, productos, categorías, proveedores, tipos, marcas |
| **Vistas** | Solo lectura general | Ver todos los módulos sin crear/editar/borrar |

### Flujo de Verificación

```
Request → Route → permission middleware
    ↓
Spatie\Permission\Middleware\PermissionMiddleware
    ↓
$user->can($permissionName)
    ↓
Revisa: model_has_permissions + role_has_permissions
    ↓
true → continua / false → 403 Forbidden
```

### Uso en Controladores

```php
// En constructor
$this->middleware('permission:ver-venta|crear-venta|editar-venta|borrar-venta', ['only' => ['index']]);
$this->middleware('permission:crear-venta', ['only' => ['create','store']]);

// En rutas API
Route::middleware('permission:ver-venta')->get('/api/ventas', [VentaController::class, 'indexApi']);

// En frontend
if (can('crear-venta')) { /* mostrar botón */ }
```

---

## 12. Autenticación y Sesiones

### Mecanismo: Session-Based (Breeze)

El sistema usa autenticación por sesión de Laravel (no JWT, aunque el paquete está instalado).

### Flujo de Login

```
POST /login { email, password }
    ↓
AuthenticatedSessionController@store
    ↓
Auth::attempt($credentials)
    ↓
Éxito → Session creada
    ↓
Redirect o Response JSON con user
    ↓
Frontend: GET /api/user → obtiene user + roles + permissions
    ↓
authState.user = responseData
    ↓
Router navega a dashboard
```

### Sesión

| Parámetro | Valor |
|-----------|-------|
| Driver | `database` |
| Tabla | `sessions` |
| Lifetime | 120 minutos |
| Cookie | `laravel_session` |
| CSRF | Activado (token en meta tag + header) |

### Cookies Relevantes

| Cookie | Propósito |
|--------|-----------|
| `laravel_session` | ID de sesión |
| `XSRF-TOKEN` | Token CSRF (legible por JS) |
| `remember_web_*` | Remember me (si aplica) |

### CSRF Protection

1. Blade template incluye: `<meta name="csrf-token" content="{{ csrf_token() }}">`
2. Axios lee el token y lo agrega a cada request: `X-CSRF-TOKEN`
3. Laravel verifica el token en requests POST/PUT/DELETE

### Auth State Management

```javascript
// authState.js
state.user = window.__APP_DATA?.user || null;  // Hydratado desde Blade si disponible
```

Métodos:
- `login({email, password})` → POST /login → actualiza state.user
- `logout()` → POST /logout → state.user = null → redirect /login
- `refreshUser()` → GET /api/user → actualiza state.user
- `hasRole(roleName)` → busca en `state.user.roles`
- `isAuthenticated()` → `state.user !== null`

---

## 13. Flujo de Datos: Venta Completa

### Diagrama de Secuencia

```
Frontend (Vue)                  Laravel API                      MySQL
     │                              │                              │
     │ 1. POST /api/ventas          │                              │
     │ {                            │                              │
     │   cliente_ci: "12345",       │                              │
     │   producto_id: 3,            │                              │
     │   cantidad: 2,               │                              │
     │   precio: 25.50,             │                              │
     │   tipo_pago: "efectivo",     │                              │
     │   monto_recibido: 60.00      │                              │
     │ }                            │                              │
     │─────────────────────────────>│                              │
     │                              │ 2. DB::beginTransaction()    │
     │                              │                              │
     │                              │ 3. Validate request data     │
     │                              │                              │
     │                              │ 4. SELECT * FROM productos   │
     │                              │    WHERE id = 3              │
     │                              │<────────────────────────────│
     │                              │    stock: 50 ✓               │
     │                              │                              │
     │                              │ 5. total = 25.50 * 2 = 51.00 │
     │                              │ 6. cambio = 60.00 - 51.00    │
     │                              │    = 9.00                    │
     │                              │                              │
     │                              │ 7. INSERT INTO pagos         │
     │                              │    (recibo, tipo_pago,       │
     │                              │     total_pagado,            │
     │                              │     monto_recibido, cambio,  │
     │                              │     cliente_ci)              │
     │                              │────────────────────────────>│
     │                              │    pago_id: 42               │
     │                              │                              │
     │                              │ 8. SELECT * FROM clientes    │
     │                              │    WHERE ci = "12345"        │
     │                              │<────────────────────────────│
     │                              │    cliente_id: 7             │
     │                              │                              │
     │                              │ 9. INSERT INTO ventas        │
     │                              │    (fecha_venta, suma_total, │
     │                              │     cliente_id, pago_id)     │
     │                              │────────────────────────────>│
     │                              │    venta_id: 88              │
     │                              │                              │
     │                              │ 10. INSERT INTO venta_productos
     │                              │     (id_producto, id_venta,  │
     │                              │      precio, cantidad)       │
     │                              │────────────────────────────>│
     │                              │                              │
     │                              │ 11. UPDATE productos         │
     │                              │     SET stock = 48           │
     │                              │     WHERE id = 3             │
     │                              │────────────────────────────>│
     │                              │                              │
     │                              │ 12. DB::commit()             │
     │                              │                              │
     │ 13. 201 Created              │                              │
     │ { venta, cliente, pago }     │                              │
     │<────────────────────────────│                              │
     │                              │                              │
     │ 14. Actualizar tabla ventas  │                              │
     │ 15. Cerrar modal             │                              │
     │ 16. Mostrar success message  │                              │
```

### Para Pago Mixto

```
Frontend → POST /api/ventas {
    tipo_pago: "mixto",
    pagos_mixtos: [
        { tipo_pago: "efectivo", monto: 20.00 },
        { tipo_pago: "qr", monto: 31.00 }
    ]
}

Laravel:
1. totalPagado = 20.00 + 31.00 = 51.00
2. Verificar |51.00 - 51.00| <= 0.01 ✓
3. INSERT INTO pagos (tipo_pago='mixto', total_pagado=51.00) → pago_id: 42
4. INSERT INTO pagos (tipo_pago='efectivo', total_pagado=20.00, pago_mixto_id=42) → pago_id: 43
5. INSERT INTO pagos (tipo_pago='qr', total_pagado=31.00, pago_mixto_id=42) → pago_id: 44
6. (Venta y detalle se crean normalmente con pago_id: 42)
```

### Modelo de Pago Mixto en Base de Datos

```
pagos (id: 42)
├── tipo_pago: "mixto"
├── total_pagado: 51.00
├── pago_mixto_id: NULL  ← Este es el padre
│
├── pagosHijos:
│   ├── pago (id: 43)
│   │   ├── tipo_pago: "efectivo"
│   │   ├── total_pagado: 20.00
│   │   └── pago_mixto_id: 42  ← apunta al padre
│   │
│   └── pago (id: 44)
│       ├── tipo_pago: "qr"
│       ├── total_pagado: 31.00
│       └── pago_mixto_id: 42  ← apunta al padre
```

---

## 14. Flujo de Autenticación

### Login

```
┌──────────────┐     POST /login      ┌───────────────┐
│   Usuario    │ ────────────────────> │   Laravel     │
│   ingresa    │                      │   Auth::      │
│   credenciales│                     │   attempt()   │
└──────────────┘                      └───────┬───────┘
                                              │
                        ┌─────────────────────┼─────────────────────┐
                        │ Éxito               │                     │ Fallo
                        ▼                     │                     ▼
                Session creada                │             422 Unprocessable
                Cookie set                    │             Entity
                        │                     │             { errors: {...} }
                        ▼                     │                     │
                GET /api/user                 │                     ▼
                (con roles + permissions)     │             Login muestra
                        │                     │             error message
                        ▼                     │
                200 OK                        │
                { user, roles, permissions }  │
                        │                     │
                        ▼                     │
                authState.user = data         │
                        │                     │
                        ▼                     │
                Router.push('/dashboard')     │
```

### Logout

```
POST /logout → Auth::logout() → Session destruida
    ↓
authState.user = null
    ↓
Router.push('/login')
```

### Protección de Rutas (Frontend)

```javascript
router.beforeEach(async (to, from, next) => {
    if (to.meta.requiresAuth && !authState.isAuthenticated()) {
        // Redirigir a login, guardando URL destino
        next({ name: 'login', query: { redirect: to.fullPath } });
    } else if (to.meta.requiresGuest && authState.isAuthenticated()) {
        // Usuario ya logueado → ir al dashboard
        next({ name: 'dashboard' });
    } else {
        next();
    }
});
```

### Protección de Endpoints (Backend)

```
/api/ventas
    ↓
auth middleware → ¿sesión válida? → No → 401
    ↓ Sí
permission:ver-venta middleware → ¿tiene permiso? → No → 403
    ↓ Sí
VentaController@indexApi → 200 OK con datos
```

---

## 15. Build y Despliegue

### Desarrollo

```bash
# Terminal 1: Laravel server
php artisan serve
# → http://127.0.0.1:8000

# Terminal 2: Vite dev server
npm run dev
# → Hot Module Replacement, proxy a Laravel
```

### Producción

```bash
# 1. Instalar dependencias PHP
composer install --optimize-autoloader --no-dev

# 2. Instalar dependencias JS
npm install

# 3. Build assets frontend
npm run build
# → Output en public/build/

# 4. Migrar base de datos
php artisan migrate --force

# 5. Seed (opcional, solo primera vez)
php artisan db:seed --force

# 6. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Permisos (Linux)
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 8. Configurar servidor web (nginx/apache)
# → Document root: public/
# → Rewrites a public/index.php
```

### Vite Configuration

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        host: '127.0.0.1',
    },
});
```

### Output del Build

```
public/build/
├── manifest.json          # Mapeo de assets
├── assets/
│   ├── app-*.css          # CSS compilado
│   ├── app-*.js           # JS bundle principal
│   ├── Index-*.css        # CSS de vistas (code-split)
│   ├── Index-*.js         # JS de vistas (lazy loaded)
│   ├── Dashboard-*.js     # Dashboard bundle
│   └── ...                # Otros chunks
```

---

## 16. Configuración de Entorno

### Variables Clave (`.env`)

```bash
# App
APP_NAME="Chuquis Coffee"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chuquis_coffee
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=log
```

### Configuración de Base de Datos

El sistema soporta múltiples drivers pero está configurado para MySQL:

```php
// config/database.php
'default' => env('DB_CONNECTION', 'sqlite'),
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'chuquis_coffee'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
],
```

### Configuración CORS

No hay archivo `config/cors.php` explícito. CORS se maneja implícitamente ya que el frontend y backend comparten el mismo origen (SPA en mismo dominio).

---

## 17. Seeders y Datos de Prueba

### Seeder Principal

```php
// DatabaseSeeder.php
$this->call([
    PermissionTableSeeder::class,
    CreateAdminUserSeeder::class,
    UserGerenteSeeder::class,
    UserVentasSeeder::class,
    UserVistasSeeder::class,
    UserCajeroSeeder::class,
    UserContadorSeeder::class,
    TipoSeeder::class,
    CategoriaSeeder::class,
    MarcaSeeder::class,
    ProveedorSeeder::class,
    ProductoSeeder::class,
    ProveedorProductoSeeder::class,
    ClienteSeeder::class,
    PagoSeeder::class,
    VentaSeeder::class,
    VentaProductoSeeder::class,
]);
```

### Usuarios por Defecto

| Email | Password | Rol | Seeder |
|-------|----------|-----|--------|
| admin@gmail.com | 12345678 | Admin | CreateAdminUserSeeder |
| gerente@gmail.com | 12345678 | Gerente | UserGerenteSeeder |
| ventas@gmail.com | 12345678 | Ventas | UserVentasSeeder |
| cajero@gmail.com | 12345678 | Cajero | UserCajeroSeeder |
| contador@gmail.com | 12345678 | Contador | UserContadorSeeder |
| vistas@gmail.com | 12345678 | Vistas | UserVistasSeeder |

### Datos de Prueba Generados

| Tabla | Cantidad Aproximada | Seeder |
|-------|--------------------|--------|
| Tipos | ~3 | TipoSeeder |
| Categorías | ~10 | CategoriaSeeder |
| Marcas | ~5 | MarcaSeeder |
| Proveedores | ~5 | ProveedorSeeder |
| Productos | ~20 | ProductoSeeder |
| Proveedores-Productos | ~15 | ProveedorProductoSeeder |
| Clientes | ~10 | ClienteSeeder |
| Pagos | ~10 | PagoSeeder |
| Ventas | ~10 | VentaSeeder |
| Venta-Productos | ~15 | VentaProductoSeeder |

---

## 18. Testing

### PHPUnit Configuration

```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Tests Existentes

| Test | Tipo | Descripción |
|------|------|-------------|
| `ExampleTest.php` | Unit | Test de ejemplo |
| `ExampleTest.php` | Feature | Test de ejemplo |
| `ProfileTest.php` | Feature | Test de perfil |
| `Auth/AuthenticationTest.php` | Feature | Login/logout |
| `Auth/EmailVerificationTest.php` | Feature | Verificación de email |
| `Auth/PasswordConfirmationTest.php` | Feature | Confirmación de contraseña |
| `Auth/PasswordResetTest.php` | Feature | Reset de contraseña |
| `Auth/RegistrationTest.php` | Feature | Registro de usuarios |

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Con coverage
php artisan test --coverage

# Solo feature tests
php artisan test --testsuite=Feature

# Test específico
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

### Base de Datos para Testing

Los tests usan SQLite en memoria por defecto (configurado en `phpunit.xml`).

---

## 19. Seguridad

### Medidas Implementadas

| Medida | Implementación |
|--------|---------------|
| **Hash de contraseñas** | bcrypt via Laravel (`'password' => 'hashed'`) |
| **CSRF Protection** | Token en meta tag + verificación en requests |
| **Session Security** | Database driver, regenerate on login |
| **Rate Limiting** | Throttle en registro (5/min), verificación email (6/min) |
| **Authorization** | RBAC via Spatie Permission |
| **Input Validation** | Form Requests + inline validation en controladores |
| **SQL Injection** | Eloquent ORM (parameterized queries) |
| **XSS Prevention** | Blade auto-escaping, Vue template escaping |
| **Hidden Passwords** | `$hidden = ['password', 'remember_token']` en modelo User |

### Áreas de Mejora

1. **Contraseñas por defecto**: Todos los seeders usan `12345678`
2. **APP_DEBUG=true**: No apto para producción
3. **JWT no utilizado**: `tymon/jwt-auth` está instalado pero no se usa (sesión activa)
4. **Sin 2FA**: No hay autenticación de dos factores
5. **Sin audit log**: No se registran cambios de datos
6. **DB_PASSWORD vacía**: En desarrollo local sin contraseña
7. **Sin HTTPS**: Desarrollo en HTTP plano

### Recomendaciones

```bash
# Producción
APP_DEBUG=false
APP_ENV=production
DB_PASSWORD=<strong_password>
# Configurar HTTPS en servidor web
# Rotar contraseñas de seeders
# Implementar audit logging
# Agregar 2FA para admin
```

---

## 20. Consideraciones de Performance

### Optimizaciones Actuales

| Técnica | Ubicación | Descripción |
|---------|-----------|-------------|
| **Eager Loading** | Controladores | `with(['cliente', 'pago', 'ventaProductos'])` |
| **Paginación** | Todos los listados | `paginate(10)` por defecto |
| **Code Splitting** | Vue Router | `component: () => import(...)` lazy loading |
| **Vite** | Build tool | Tree-shaking, minificación |
| **DB Indexes** | Migrations | FKs indexadas automáticamente, UNIQUE en CI/email |
| **Permission Cache** | Spatie | Cache de 24 horas |
| **Select * avoidance** | Queries | Eloquent carga solo columnas necesarias |

### Queries Críticos

| Query | Tabla | Frecuencia | Optimización |
|-------|-------|-----------|-------------|
| `GET /api/ventas` | ventas + pago + cliente | Alta | Paginado, eager loading |
| `GET /api/productos` | productos + categoria + marca | Alta | Paginado |
| Dashboard queries | ventas, pagos, productos | Media (cacheable) | Aggregation queries |
| `GET /api/user` | users + roles + permissions | Cada navegación | Eager loading, cache |

### Escalamiento Potencial

| Área | Estrategia |
|------|-----------|
| **Cache** | Redis para permisos, dashboard data, consultas frecuentes |
| **Queue** | Mover PDF generation a jobs async |
| **CDN** | Servir assets estáticos (imágenes, JS, CSS) desde CDN |
| **DB Read Replicas** | Separar reads de writes para reportes |
| **API Rate Limiting** | Agregar throttle a endpoints API |
| **Pagination defaults** | Permitir `per_page` configurable |
| **Search** | Agregar índices full-text para búsqueda en productos/clientes |

### Limitaciones Conocidas

- `productos.precio` es DECIMAL(3,2) → máximo 9.99. Considerar DECIMAL(8,2) para precios mayores.
- `ventas.suma_total` es DECIMAL(5,2) → máximo 999.99. Considerar DECIMAL(10,2) para ventas grandes.
- `venta_productos.precio` es DECIMAL(3,2) → mismo límite.
- No hay soft deletes en ventas/productos (eliminación permanente).
- Las imágenes de productos se guardan en `storage/app/public/productos/` sin optimización de tamaño.
