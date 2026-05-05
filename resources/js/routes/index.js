import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const routes = [
    {
        path: '/',
        name: 'welcome',
        component: () => import('@/views/public/Welcome.vue'),
        meta: { title: 'Inicio' },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/Login.vue'),
        meta: { layout: AuthLayout, requiresGuest: true, title: 'Iniciar Sesión' },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/auth/Register.vue'),
        meta: { layout: AuthLayout, requiresGuest: true, title: 'Registro' },
    },
    {
        path: '/forgot-password',
        name: 'password.request',
        component: () => import('@/views/auth/ForgotPassword.vue'),
        meta: { layout: AuthLayout, requiresGuest: true, title: 'Recuperar Contraseña' },
    },
    {
        path: '/reset-password/:token',
        name: 'password.reset',
        component: () => import('@/views/auth/ResetPassword.vue'),
        meta: { layout: AuthLayout, requiresGuest: true, title: 'Nueva Contraseña' },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/dashboard/Dashboard.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Dashboard' },
    },
    {
        path: '/admin',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/AdminDashboard.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Panel de Administración' },
    },
    {
        path: '/profile',
        name: 'profile.edit',
        component: () => import('@/views/profile/ProfileEdit.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Mi Perfil' },
    },
    {
        path: '/clientes',
        name: 'clientes.index',
        component: () => import('@/views/clientes/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Clientes' },
    },
    {
        path: '/categorias',
        name: 'categorias.index',
        component: () => import('@/views/categorias/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Categorías' },
    },
    {
        path: '/marcas',
        name: 'marcas.index',
        component: () => import('@/views/marcas/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Marcas' },
    },
    {
        path: '/tipos',
        name: 'tipos.index',
        component: () => import('@/views/tipos/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Tipos' },
    },
    {
        path: '/productos',
        name: 'productos.index',
        component: () => import('@/views/productos/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Productos' },
    },
    {
        path: '/proveedores',
        name: 'proveedores.index',
        component: () => import('@/views/proveedores/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Proveedores' },
    },
    {
        path: '/proveedores-productos',
        name: 'proveedores-productos.index',
        component: () => import('@/views/proveedores-producto/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Proveedor-Producto' },
    },
    {
        path: '/ventas',
        name: 'ventas.index',
        component: () => import('@/views/ventas/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Ventas' },
    },
    {
        path: '/pagos',
        name: 'pagos.index',
        component: () => import('@/views/pagos/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Pagos' },
    },
    {
        path: '/pagos-mixto',
        name: 'pagos-mixto.index',
        component: () => import('@/views/pagos-mixto/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Pagos Mixtos' },
    },
    {
        path: '/reportes',
        name: 'reportes.index',
        component: () => import('@/views/reportes/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Reportes' },
    },
    {
        path: '/roles',
        name: 'roles.index',
        component: () => import('@/views/roles/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Roles' },
    },
    {
        path: '/users',
        name: 'users.index',
        component: () => import('@/views/users/Index.vue'),
        meta: { layout: AppLayout, requiresAuth: true, title: 'Usuarios' },
    },
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: () => import('@/views/public/NotFound.vue'),
        meta: { title: '404 - No Encontrado' },
    },
];

export default routes;
