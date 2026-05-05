<template>
    <div>
        <!-- Loading -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando panel de administración...</p>
        </div>

        <template v-else>
            <!-- Quick Access Buttons -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="text-gold mb-3"><i class="fas fa-bolt mr-2"></i>Accesos Rápidos</h5>
                </div>
                <div class="col-md-4 col-sm-6 mb-3" v-for="access in quickAccess" :key="access.route">
                    <router-link :to="access.route" class="quick-access-card text-decoration-none">
                        <i :class="access.icon"></i>
                        <span>{{ access.label }}</span>
                    </router-link>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-xl-4 col-lg-6 col-md-6 mb-3" v-for="(stat, key) in estadisticas" :key="key">
                    <div class="stat-card" :style="{ borderLeftColor: getStatColor(stat.color) }">
                        <div class="stat-icon" :style="{ background: getStatGradient(stat.color) }">
                            <i :class="stat.icon"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">{{ stat.titulo }}</span>
                            <span class="stat-value">
                                <template v-if="stat.ingresos !== undefined">
                                    ${{ formatNumber(stat.ingresos) }}
                                </template>
                                <template v-else>
                                    {{ formatNumber(stat.total) }}
                                </template>
                            </span>
                            <small v-if="stat.ingresos !== undefined" class="stat-detail">
                                <i class="fas fa-shopping-cart mr-1"></i>{{ formatNumber(stat.total) }} ventas
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts + Recent Sales -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bell text-gold"></i> Alertas del Sistema</h3>
                            <span class="badge bg-coffee float-right">{{ alertas?.length || 0 }} alertas</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2" v-if="alertas?.length">
                                <li class="item alert-item" v-for="(alerta, i) in alertas" :key="i">
                                    <div class="product-info">
                                        <a href="#" class="product-title">
                                            <i :class="[alerta.icon, getTextColor(alerta.tipo)]"></i>
                                            {{ alerta.mensaje }}
                                            <span :class="'badge ' + getBadgeClass(alerta.tipo) + ' float-right'">{{ alerta.fecha }}</span>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                            <div class="text-center p-5" v-else>
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">Sin alertas en este momento</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-history text-gold"></i> Ventas Recientes</h3>
                            <router-link to="/ventas" class="btn btn-sm btn-coffee float-right">
                                <i class="fas fa-list"></i> Ver Todas
                            </router-link>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" v-if="ventasRecientes?.length">
                                <table class="table table-sm table-hover table-coffee">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="venta in ventasRecientes" :key="venta.id" class="table-row-hover">
                                            <td><i class="fas fa-coffee text-gold mr-2"></i>{{ venta.producto?.nombre || 'N/A' }}</td>
                                            <td>{{ venta.cliente?.nombres || 'Consumidor Final' }}</td>
                                            <td><strong class="text-gold">${{ formatNumber(venta.suma_total ?? 0) }}</strong></td>
                                            <td class="text-muted">{{ formatDate(venta.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center p-5" v-else>
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay ventas recientes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import http from '@/services/http';

const estadisticas = ref({});
const alertas = ref([]);
const ventasRecientes = ref([]);
const loading = ref(true);
let refreshInterval = null;

const quickAccess = [
    { route: '/users', icon: 'fas fa-users-cog', label: 'Usuarios' },
    { route: '/roles', icon: 'fas fa-user-shield', label: 'Roles' },
    { route: '/productos', icon: 'fas fa-box', label: 'Productos' },
    { route: '/proveedores', icon: 'fas fa-truck', label: 'Proveedores' },
    { route: '/ventas', icon: 'fas fa-shopping-cart', label: 'Ventas' },
    { route: '/reportes', icon: 'fas fa-chart-bar', label: 'Reportes' },
];

// ── Palette ──────────────────────────────────────────────────────────────────

const colorMap = {
    primary: { solid: '#8B4513', gradient: 'linear-gradient(135deg,#8B4513,#A0522D)' },
    success: { solid: '#2E7D32', gradient: 'linear-gradient(135deg,#2E7D32,#388E3C)' },
    warning: { solid: '#B8860B', gradient: 'linear-gradient(135deg,#B8860B,#DAA520)' },
    danger:  { solid: '#C62828', gradient: 'linear-gradient(135deg,#C62828,#D32F2F)' },
    info:    { solid: '#1565C0', gradient: 'linear-gradient(135deg,#1565C0,#1976D2)' },
};

// ── Helpers ───────────────────────────────────────────────────────────────────

function getStatColor(color)    { return colorMap[color]?.solid    || '#8B4513'; }
function getStatGradient(color) { return colorMap[color]?.gradient || 'linear-gradient(135deg,#8B4513,#A0522D)'; }
function getBadgeClass(tipo)    { return { danger:'bg-danger', warning:'bg-warning', info:'bg-info', success:'bg-success' }[tipo] || 'bg-secondary'; }
function getTextColor(tipo)     { return { danger:'text-danger', warning:'text-warning', info:'text-info', success:'text-success' }[tipo] || 'text-muted'; }

function formatNumber(num) {
    return Number(num || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' }) + ' ' +
           d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

// ── Data loading ──────────────────────────────────────────────────────────────

async function loadDashboard() {
    try {
        const response = await http.get('/api/admin/dashboard');
        const data = response.data;
        if (data.success) {
            estadisticas.value = data.estadisticas || {};
            alertas.value = data.alertas || [];
            ventasRecientes.value = data.ventasRecientes || [];
            loading.value = false;
        }
    } catch (e) {
        console.error('Error cargando admin dashboard:', e);
        loading.value = false;
    }
}

onMounted(() => {
    loadDashboard();
    refreshInterval = setInterval(loadDashboard, 120000);
});

onUnmounted(() => {
    if (refreshInterval) clearInterval(refreshInterval);
});
</script>

<style scoped>
.stat-card {
    background: linear-gradient(145deg, #2c1810, #1a0f0a);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    border-left: 4px solid #b8860b;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100px;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 100px; height: 100px;
    background: radial-gradient(circle, rgba(240,208,138,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    border-left-width: 6px;
}

.stat-icon {
    width: 60px; height: 60px; min-width: 60px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: #fff;
    margin-right: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.stat-content {
    display: flex; flex-direction: column; flex: 1;
}

.stat-label {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.3rem;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #f0d08a;
    font-family: 'Cormorant Garamond', serif;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.stat-detail {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    margin-top: 0.2rem;
}

.chart-card {
    background: linear-gradient(145deg, #2c1810, #1a0f0a);
    border-radius: 16px;
    border: 1px solid rgba(230,200,124,0.2);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    overflow: hidden;
}

.chart-card .card-header {
    background: rgba(26,15,10,0.5);
    border-bottom: 1px solid rgba(230,200,124,0.2);
    padding: 1.2rem 1.5rem;
    display: flex; align-items: center; justify-content: space-between;
}

.chart-card .card-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.3rem;
    color: #f0d08a;
    margin: 0;
}

.chart-card .card-body { padding: 1.5rem; }

.text-gold  { color: #e6c87c !important; }
.bg-coffee  { background: rgba(62,39,35,0.8) !important; color: #e6c87c; font-size: 0.75rem; padding: 0.3rem 0.8rem; border-radius: 25px; }

.quick-access-card {
    background: linear-gradient(145deg, #2c1810, #1a0f0a);
    border: 1px solid rgba(230,200,124,0.2);
    border-radius: 12px;
    padding: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
    color: #e6c87c;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    min-height: 60px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.quick-access-card:hover {
    background: linear-gradient(145deg, #3c2820, #2a1f1a);
    border-color: rgba(230,200,124,0.4);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.4);
    color: #f0d08a;
}

.quick-access-card i {
    font-size: 1.3rem;
}

.btn-coffee {
    background: linear-gradient(135deg, #e6c87c, #b8860b);
    border: none; color: #1a0f0a; font-weight: 600;
}
.btn-coffee:hover {
    background: linear-gradient(135deg, #f0d08a, #e6c87c);
    color: #1a0f0a; transform: translateY(-1px);
}

.alert-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(230,200,124,0.1);
    transition: background 0.3s ease;
}
.alert-item:hover { background: rgba(230,200,124,0.05); }
.alert-item .product-title { color: rgba(255,255,255,0.8); font-size: 0.95rem; }
.alert-item .product-title:hover { color: #f0d08a; }

.table-coffee { color: rgba(255,255,255,0.8); }
.table-coffee thead th {
    background: rgba(26,15,10,0.5);
    color: #e6c87c;
    border-bottom: 2px solid rgba(230,200,124,0.2);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 1rem;
}
.table-coffee tbody td {
    border-bottom: 1px solid rgba(230,200,124,0.1);
    padding: 0.8rem 1rem;
    vertical-align: middle;
}
.table-row-hover:hover { background: rgba(230,200,124,0.05) !important; }

@media (max-width: 1200px) {
    .stat-card { padding: 1.2rem; }
    .stat-value { font-size: 1.5rem; }
    .stat-icon  { width: 50px; height: 50px; min-width: 50px; font-size: 1.3rem; }
}

@media (max-width: 992px) {
    .stat-card { padding: 1rem; }
    .stat-value { font-size: 1.3rem; }
    .chart-card .card-title { font-size: 1.1rem; }
}

@media (max-width: 768px) {
    .stat-value { font-size: 1.5rem; }
    .chart-card .card-header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
}

@media (max-width: 576px) {
    .stat-card { padding: 0.8rem; }
    .stat-icon  { width: 45px; height: 45px; min-width: 45px; font-size: 1.2rem; margin-right: 0.8rem; }
    .stat-value { font-size: 1.3rem; }
    .stat-label { font-size: 0.75rem; }
    .chart-card .card-body { padding: 1rem; }
    .table-coffee thead th, .table-coffee tbody td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
}
</style>
