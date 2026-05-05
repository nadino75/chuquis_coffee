<template>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#" @click.prevent="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <router-link to="/" class="nav-link">Home</router-link>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-3 d-none d-sm-flex align-items-center">
                    <span class="badge role-badge">
                        <i class="fas fa-id-badge mr-1"></i>{{ userRole || 'Usuario' }}
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" @click.prevent="toggleUserMenu">
                        <i class="fas fa-user mr-1"></i> {{ user?.name || 'Usuario' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" v-show="userMenuOpen">
                        <router-link to="/profile" class="dropdown-item">
                            <i class="fas fa-user-edit mr-1"></i> Perfil
                        </router-link>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item" @click.prevent="handleLogout">
                            <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4" :class="{ 'sidebar-collapse': sidebarCollapsed }">
            <router-link to="/dashboard" class="brand-link">
                <span class="brand-text font-weight-light">Chuquis Coffee</span>
            </router-link>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                        <!-- Dashboard — todos -->
                        <li class="nav-item">
                            <router-link to="/dashboard" class="nav-link" active-class="active" exact-active-class="active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </router-link>
                        </li>

                        <!-- ── OPERACIONES (Cajero / Ventas / Admin / Gerente) ── -->
                        <template v-if="can('ver-cliente')">
                            <li class="nav-header">Operaciones</li>
                            <li class="nav-item">
                                <router-link to="/clientes" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Clientes</p>
                                </router-link>
                            </li>
                        </template>

                        <li class="nav-item" v-if="can('ver-venta')">
                            <router-link to="/ventas" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>Ventas</p>
                            </router-link>
                        </li>

                        <li class="nav-item" v-if="can('ver-pago')">
                            <router-link to="/pagos" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>Pagos</p>
                            </router-link>
                        </li>

                        <li class="nav-item" v-if="can('ver-pago') && !isCajero && !isContador">
                            <router-link to="/pagos-mixto" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Pagos Mixtos</p>
                            </router-link>
                        </li>

                        <!-- ── CATÁLOGO (no Cajero, no Contador) ── -->
                        <template v-if="!isCajero && can('ver-producto')">
                            <li class="nav-header">Catálogo</li>
                            <li class="nav-item">
                                <router-link to="/productos" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-box"></i>
                                    <p>Productos</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="can('ver-categoria')">
                                <router-link to="/categorias" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-tags"></i>
                                    <p>Categorías</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="can('ver-marca')">
                                <router-link to="/marcas" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-award"></i>
                                    <p>Marcas</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="can('ver-tipo')">
                                <router-link to="/tipos" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-layer-group"></i>
                                    <p>Tipos</p>
                                </router-link>
                            </li>
                        </template>

                        <!-- Ver productos (solo Cajero) -->
                        <template v-if="isCajero">
                            <li class="nav-header">Inventario</li>
                            <li class="nav-item">
                                <router-link to="/productos" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-box"></i>
                                    <p>Productos</p>
                                </router-link>
                            </li>
                        </template>

                        <!-- ── PROVEEDORES (Admin / Gerente) ── -->
                        <template v-if="can('ver-proveedor') && !isCajero && !isContador">
                            <li class="nav-header">Proveedores</li>
                            <li class="nav-item">
                                <router-link to="/proveedores" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-truck"></i>
                                    <p>Proveedores</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="isAdmin">
                                <router-link to="/proveedores-productos" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-link"></i>
                                    <p>Prov-Producto</p>
                                </router-link>
                            </li>
                        </template>

                        <!-- ── ADMINISTRACIÓN ── -->
                        <template v-if="!isCajero">
                            <li class="nav-header">Administración</li>
                            <li class="nav-item" v-if="isAdmin">
                                <router-link to="/admin" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-cogs"></i>
                                    <p>Panel de Administración</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="isContador || isAdmin || isGerente">
                                <router-link to="/reportes" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Reportes</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="can('ver-rol')">
                                <router-link to="/roles" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-user-shield"></i>
                                    <p>Roles</p>
                                </router-link>
                            </li>
                            <li class="nav-item" v-if="can('ver-usuario')">
                                <router-link to="/users" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-user-cog"></i>
                                    <p>Usuarios</p>
                                </router-link>
                            </li>
                        </template>

                        <!-- ── SESIÓN ── -->
                        <li class="nav-header">Sesión</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" @click.prevent="handleLogout">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Cerrar Sesión</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>{{ pageTitle }}</h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <slot></slot>
                </div>
            </section>
        </div>

        <footer class="main-footer text-sm text-center">
            <strong>&copy; {{ new Date().getFullYear() }} Chuquis Coffee.</strong> Todos los derechos reservados.
        </footer>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import authState from '@/services/authState';

const router = useRouter();
const sidebarCollapsed = ref(false);
const userMenuOpen = ref(false);
const user = ref(authState.user);

const pageTitle = computed(() => router.currentRoute.value.meta.title || 'Chuquis Coffee');

const userRole = computed(() => user.value?.roles?.[0]?.name || 'Usuario');
const isAdmin    = computed(() => user.value?.roles?.some(r => r.name === 'Admin') ?? false);
const isGerente  = computed(() => user.value?.roles?.some(r => r.name === 'Gerente') ?? false);
const isCajero   = computed(() => user.value?.roles?.some(r => r.name === 'Cajero') ?? false);
const isContador = computed(() => user.value?.roles?.some(r => r.name === 'Contador') ?? false);

// Check if user has a given permission name via their role's permissions
function can(permission) {
    const u = user.value;
    if (!u) return false;
    // Admin always has all permissions
    if (u.roles?.some(r => r.name === 'Admin')) return true;
    // Check via roles[x].permissions[]
    return u.roles?.some(role =>
        role.permissions?.some(p => p.name === permission)
    ) ?? false;
}

function toggleSidebar() { sidebarCollapsed.value = !sidebarCollapsed.value; }
function toggleUserMenu() { userMenuOpen.value = !userMenuOpen.value; }

async function handleLogout() {
    await authState.logout();
    user.value = null;
    router.push({ name: 'login' });
}

async function refreshUser() {
    try {
        const fresh = await authState.refreshUser();
        user.value = fresh;
    } catch (e) {}
}

if (!user.value) refreshUser();

document.addEventListener('click', (e) => {
    if (!e.target.closest('.nav-item.dropdown')) userMenuOpen.value = false;
});
</script>

<style scoped>
.brand-link {
    background: linear-gradient(90deg, #1a1a1a, #3e2723);
    color: #daa520 !important;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 1.3rem;
    padding: 12px 20px;
    text-align: center;
}

.brand-text { color: #daa520; }

.nav-link.active {
    background: #3e2723;
    color: #daa520;
}

.nav-link:hover { background: rgba(218, 165, 32, 0.1); }

.role-badge {
    background: rgba(218, 165, 32, 0.2);
    color: #daa520;
    border: 1px solid rgba(218, 165, 32, 0.4);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}
</style>
