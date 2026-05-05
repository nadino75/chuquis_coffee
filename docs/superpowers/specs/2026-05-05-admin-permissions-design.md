# Admin Dashboard & Permission System - Design Spec

**Date:** 2026-05-05
**Project:** Chuquis Coffee

## Problem Summary

1. `/api/user` does not load role permissions, causing the sidebar's `can()` function to fail. Admin users see an empty sidebar.
2. No API endpoints have permission middleware. Only `RoleController` has permission middleware on its web (view) methods, not on `*Api` methods.
3. Admin lacks a dedicated dashboard for system-wide control and monitoring.

## Section 1: Fix `/api/user` Endpoint

**File:** `routes/web.php`

Change the authenticated user endpoint to load roles with their permissions:

```php
// Before
return response()->json(Auth::user()->load('roles:id,name'));

// After
return response()->json(Auth::user()->load('roles:id,name,permissions:id,name'));
```

This provides `user.roles[].permissions[]` data that `can()` in `AppLayout.vue` requires to render sidebar items correctly.

## Section 2: Permission Middleware

The `spatie/laravel-permission` package already provides and registers the `permission` middleware in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

**No custom middleware needed.** The package's `PermissionMiddleware` checks if the user has the required permission via their roles. Users with the `Admin` role (which has all permissions assigned) will automatically pass all permission checks.

**Usage:** `middleware('permission:crear-usuario')`

## Section 3: Apply Permissions to API Routes

**File:** `routes/api.php`

Each resource groups endpoints by permission type. All routes already require `auth` middleware. Permission middleware wraps each CRUD group.

### Permission Mapping

| Resource | Index/Show | Store | Update | Destroy |
|---|---|---|---|---|
| Users | `ver-usuario` | `crear-usuario` | `editar-usuario` | `borrar-usuario` |
| Roles | `ver-rol` | `crear-rol` | `editar-rol` | `borrar-rol` |
| Clientes | `ver-cliente` | `crear-cliente` | `editar-cliente` | `borrar-cliente` |
| Productos | `ver-producto` | `crear-producto` | `editar-producto` | `borrar-producto` |
| Categorias | `ver-categoria` | `crear-categoria` | `editar-categoria` | `borrar-categoria` |
| Marcas | `ver-marca` | `crear-marca` | `editar-marca` | `borrar-marca` |
| Tipos | `ver-tipo` | `crear-tipo` | `editar-tipo` | `borrar-tipo` |
| Proveedores | `ver-proveedor` | `crear-proveedor` | `editar-proveedor` | `borrar-proveedor` |
| Ventas | `ver-venta` | `crear-venta` | `editar-venta` | `borrar-venta` |
| Pagos | `ver-pago` | `crear-pago` | `editar-pago` | `borrar-pago` |
| Proveedores-Producto | `ver-proveedor` | `crear-proveedor` | `editar-proveedor` | `borrar-proveedor` |
| Pagos-Mixto | `ver-pago` | `crear-pago` | `editar-pago` | `borrar-pago` |

### Example Route Structure

```php
// Users
Route::middleware('permission:ver-usuario')->group(function () {
    Route::get('/api/users', [UserController::class, 'indexApi']);
    Route::get('/api/users/{id}', [UserController::class, 'showApi']);
});
Route::middleware('permission:crear-usuario')->group(function () {
    Route::post('/api/users', [UserController::class, 'storeApi']);
});
Route::middleware('permission:editar-usuario')->group(function () {
    Route::put('/api/users/{id}', [UserController::class, 'updateApi']);
});
Route::middleware('permission:borrar-usuario')->group(function () {
    Route::delete('/api/users/{id}', [UserController::class, 'destroyApi']);
});
```

### Dashboard Route

The dashboard route does not require individual permissions. It uses role-based logic inside the controller (Cajero, Contador, Admin/General).

## Section 4: Admin Dashboard

### New File: `resources/js/views/admin/AdminDashboard.vue`

Dedicated admin dashboard component with:

1. **Global Metrics** — 6 stat cards:
   - Usuarios activos (total users count)
   - Roles configurados (total roles count)
   - Clientes registrados (total clients)
   - Productos en catálogo (total products)
   - Ventas totales (total sales count + revenue)
   - Ingresos totales (all-time revenue)

2. **Quick Access Buttons** — Direct links to:
   - Usuarios (`/users`)
   - Roles (`/roles`)
   - Productos (`/productos`)
   - Proveedores (`/proveedores`)
   - Ventas (`/ventas`)
   - Reportes (`/reportes`)

3. **Recent Activity** — Last 10 sales with client, total, and date

4. **System Alerts** — Low stock / out of stock products (reuses existing `alertasSistema()` logic)

### Backend: `app/Http/Controllers/AdminDashboardController.php`

New controller with `obtenerDatosAdmin()` method that returns:

```json
{
    "success": true,
    "estadisticas": {
        "total_usuarios": { "total": 5, "icon": "fas fa-users", "color": "primary", "titulo": "Usuarios Activos" },
        "total_roles": { "total": 6, "icon": "fas fa-user-shield", "color": "warning", "titulo": "Roles" },
        "total_clientes": { "total": 50, "icon": "fas fa-user-friends", "color": "info", "titulo": "Clientes" },
        "total_productos": { "total": 30, "icon": "fas fa-box", "color": "success", "titulo": "Productos" },
        "ventas_totales": { "total": 100, "ingresos": 5000.00, "icon": "fas fa-shopping-cart", "color": "info", "titulo": "Ventas Totales" },
        "ingresos_totales": { "total": 5000.00, "icon": "fas fa-dollar-sign", "color": "success", "titulo": "Ingresos Totales" }
    },
    "alertas": [...],
    "ventasRecientes": [...]
}
```

### New Route: `routes/api.php`

```php
Route::middleware('auth')->get('/api/admin/dashboard', [AdminDashboardController::class, 'obtenerDatosAdmin']);
```

### New Vue Route: `resources/js/routes/index.js`

```js
{
    path: '/admin',
    name: 'admin.dashboard',
    component: () => import('@/views/admin/AdminDashboard.vue'),
    meta: { layout: AppLayout, requiresAuth: true, title: 'Panel de Administración' },
},
```

### Sidebar Update: `resources/js/layouts/AppLayout.vue`

Add `/admin` link in the "Administración" section, visible only to `isAdmin`:

```vue
<li class="nav-item" v-if="isAdmin">
    <router-link to="/admin" class="nav-link" active-class="active">
        <i class="nav-icon fas fa-cogs"></i>
        <p>Panel de Administración</p>
    </router-link>
</li>
```

Placed before the Reportes link in the Administración section.

## Files to Create

1. `app/Http/Controllers/AdminDashboardController.php`
2. `resources/js/views/admin/AdminDashboard.vue`

## Files to Modify

1. `routes/web.php` — Add `permissions` to user API response
2. `routes/api.php` — Wrap all CRUD routes with permission middleware; add admin dashboard route
3. `resources/js/routes/index.js` — Add `/admin` route
4. `resources/js/layouts/AppLayout.vue` — Add admin dashboard link to sidebar

## Dependencies

- Uses existing `spatie/laravel-permission` package (already installed)
- Uses existing `PermissionTableSeeder` permissions (all needed permissions already exist)
- Uses existing models: `User`, `Role`, `Venta`, `Producto`, `Cliente`
- Uses existing chart styling from `Dashboard.vue` (coffee theme colors, stat-card styles)

## Error Handling

- Permission middleware returns `403` with `{ "success": false, "message": "No tienes permiso para realizar esta acción." }`
- Admin dashboard returns empty arrays for alerts/recent sales if no data exists
- All queries use safe defaults (0 for counts, empty arrays for lists)
