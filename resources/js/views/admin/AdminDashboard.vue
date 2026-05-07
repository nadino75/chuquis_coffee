<template>
    <div class="dashboard-container" role="main" aria-label="Panel de administración">
        <a href="#admin-dashboard-content" class="skip-link">Saltar al contenido principal</a>

        <!-- Loading state -->
        <div v-if="loading" class="loading-state" role="status" aria-live="polite">
            <div class="spinner" aria-hidden="true">
                <div class="spinner-ring"></div>
            </div>
            <p class="loading-text">Cargando panel de administración...</p>
        </div>

        <div id="admin-dashboard-content" v-else>
            <h1 class="sr-only">Dashboard de Administración</h1>

            <!-- Quick Access Cards -->
            <section class="quick-access-section" aria-label="Accesos rápidos">
                <h2 class="section-heading">
                    <i class="fas fa-bolt" aria-hidden="true"></i> Accesos Rápidos
                </h2>
                <div class="quick-access-grid">
                    <router-link
                        v-for="access in quickAccess"
                        :key="access.route"
                        :to="access.route"
                        class="quick-access-card"
                        :aria-label="`Ir a ${access.label}`"
                    >
                        <i :class="access.icon" aria-hidden="true"></i>
                        <span>{{ access.label }}</span>
                    </router-link>
                </div>
            </section>

            <!-- Statistics -->
            <section class="stats-grid" aria-label="Estadísticas del sistema" aria-live="polite">
                <article
                    v-for="(stat, key) in estadisticas"
                    :key="key"
                    class="stat-card"
                    :style="{ borderLeftColor: getStatColor(stat.color) }"
                    role="img"
                    :aria-label="`${stat.titulo}: ${stat.ingresos !== undefined ? 'Bs. ' + formatNumber(stat.ingresos ?? stat.total) : formatNumber(stat.total)}`"
                >
                    <div class="stat-card__icon" :style="{ background: getStatGradient(stat.color) }" aria-hidden="true">
                        <i :class="stat.icon"></i>
                    </div>
                    <div class="stat-card__content">
                        <span class="stat-card__label">{{ stat.titulo }}</span>
                        <span class="stat-card__value">
                            <template v-if="stat.ingresos !== undefined">Bs. {{ formatNumber(stat.ingresos) }}</template>
                            <template v-else>{{ formatNumber(stat.total) }}</template>
                        </span>
                        <small v-if="stat.ingresos !== undefined" class="stat-card__detail">
                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                            {{ formatNumber(stat.total) }} ventas
                        </small>
                    </div>
                </article>
            </section>

            <!-- Alerts + Recent Sales -->
            <div class="tables-grid">
                <section class="chart-card" aria-label="Alertas del sistema">
                    <div class="chart-card__header">
                        <h2 class="chart-card__title">
                            <i class="fas fa-bell" aria-hidden="true"></i> Alertas del Sistema
                        </h2>
                        <span class="chart-card__badge">{{ alertas?.length || 0 }} alertas</span>
                    </div>
                    <div class="chart-card__body chart-card__body--no-pad">
                        <ul v-if="alertas?.length" class="alert-list" aria-label="Lista de alertas del sistema">
                            <li v-for="(alerta, i) in alertas" :key="i" class="alert-list__item">
                                <i :class="[alerta.icon, getTextColor(alerta.tipo)]" aria-hidden="true"></i>
                                <span :class="getTextColor(alerta.tipo)">{{ alerta.mensaje }}</span>
                                <span :class="'badge ' + getBadgeClass(alerta.tipo)">{{ alerta.fecha }}</span>
                            </li>
                        </ul>
                        <div v-else class="chart-empty" role="img" aria-label="Sin alertas en este momento">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            <p>Sin alertas en este momento</p>
                        </div>
                    </div>
                </section>

                <section class="chart-card" aria-label="Ventas recientes">
                    <div class="chart-card__header">
                        <h2 class="chart-card__title">
                            <i class="fas fa-history" aria-hidden="true"></i> Ventas Recientes
                        </h2>
                        <router-link to="/ventas" class="chart-card__link">
                            <i class="fas fa-list" aria-hidden="true"></i> Ver Todas
                        </router-link>
                    </div>
                    <div class="chart-card__body chart-card__body--no-pad">
                        <div class="table-responsive-wrapper">
                            <table class="data-table" aria-label="Lista de ventas recientes">
                                <caption class="sr-only">Ventas recientes con detalles de producto, cliente, total y fecha</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="venta in ventasRecientes" :key="venta.id">
                                        <td><i class="fas fa-coffee" aria-hidden="true"></i> {{ venta.producto?.nombre || 'N/A' }}</td>
                                        <td>{{ venta.cliente?.nombres || 'Consumidor Final' }}</td>
                                        <td class="text-gold"><strong>Bs. {{ formatNumber(venta.suma_total ?? 0) }}</strong></td>
                                        <td class="text-muted">{{ formatDate(venta.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="!ventasRecientes?.length" class="chart-empty" role="img" aria-label="Sin ventas recientes">
                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                            <p>No hay ventas recientes</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
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

// ── Palette ──────────────────────────────────────────────────────────────

const colorMap = {
    primary: { solid: '#8B4513', gradient: 'linear-gradient(135deg,#8B4513,#A0522D)' },
    success: { solid: '#2E7D32', gradient: 'linear-gradient(135deg,#2E7D32,#388E3C)' },
    warning: { solid: '#B8860B', gradient: 'linear-gradient(135deg,#B8860B,#DAA520)' },
    danger:  { solid: '#C62828', gradient: 'linear-gradient(135deg,#C62828,#D32F2F)' },
    info:    { solid: '#1565C0', gradient: 'linear-gradient(135deg,#1565C0,#1976D2)' },
};

// ── Helpers ─────────────────────────────────────────────────────────────

function getStatColor(color)    { return colorMap[color]?.solid    || '#8B4513'; }
function getStatGradient(color) { return colorMap[color]?.gradient || 'linear-gradient(135deg,#8B4513,#A0522D)'; }
function getBadgeClass(tipo)    { return { danger:'badge--danger', warning:'badge--warning', info:'badge--info', success:'badge--success' }[tipo] || 'badge--secondary'; }
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

// ── Data loading ────────────────────────────────────────────────────────

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
/* ══════════════════════════════════════════════════════════════════════════
   ADMIN DASHBOARD STYLES — ISO 9241 Compliant + Responsive + Refined Aesthetics
   ══════════════════════════════════════════════════════════════════════════ */

/* ── Layout ────────────────────────────────────────────────────────────── */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem;
    min-height: 100vh;
}

/* ── Skip Link ─────────────────────────────────────────────────────────── */
.skip-link {
    position: absolute;
    top: -100%;
    left: 1rem;
    z-index: 9999;
    padding: 0.75rem 1.5rem;
    background: #e6c87c;
    color: #1a0f0a;
    font-weight: 600;
    border-radius: 8px;
    transition: top 0.3s ease;
    text-decoration: none;
}

.skip-link:focus {
    top: 1rem;
}

/* ── Screen reader only ────────────────────────────────────────────────── */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

/* ── Loading State ─────────────────────────────────────────────────────── */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    gap: 1.5rem;
}

.spinner {
    width: 48px;
    height: 48px;
    position: relative;
}

.spinner-ring {
    width: 100%;
    height: 100%;
    border: 3px solid rgba(230, 200, 124, 0.2);
    border-top-color: #e6c87c;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    font-family: 'Manrope', sans-serif;
}

/* ── Section Heading ───────────────────────────────────────────────────── */
.section-heading {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.4rem;
    color: #e6c87c;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-heading i {
    font-size: 1.1rem;
}

/* ── Quick Access Grid ─────────────────────────────────────────────────── */
.quick-access-section {
    margin-bottom: 2rem;
}

.quick-access-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
}

.quick-access-card {
    background: linear-gradient(145deg, #2c1810, #1a0f0a);
    border: 1px solid rgba(230, 200, 124, 0.15);
    border-radius: 12px;
    padding: 1.2rem 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.7rem;
    color: #e6c87c;
    font-weight: 600;
    font-size: 0.92rem;
    font-family: 'Manrope', sans-serif;
    text-decoration: none;
    transition: all 0.3s ease;
    min-height: 60px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
}

.quick-access-card:hover,
.quick-access-card:focus-visible {
    background: linear-gradient(145deg, #3c2820, #2a1f1a);
    border-color: rgba(230, 200, 124, 0.35);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
    color: #f0d08a;
}

.quick-access-card:focus-visible {
    outline: 2px solid #e6c87c;
    outline-offset: 2px;
}

.quick-access-card i {
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* ── Stats Grid ────────────────────────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(145deg, #2c1810, #1a0f0a);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    border-left: 4px solid #b8860b;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 110px;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(240, 208, 138, 0.08) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.stat-card:hover,
.stat-card:focus-visible {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    border-left-width: 6px;
}

.stat-card__icon {
    width: 56px;
    height: 56px;
    min-width: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: #fff;
    margin-right: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    flex-shrink: 0;
}

.stat-card__content {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
}

.stat-card__label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 0.25rem;
    font-family: 'Manrope', sans-serif;
    font-weight: 500;
}

.stat-card__value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #f0d08a;
    font-family: 'Cormorant Garamond', serif;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    line-height: 1.2;
    word-break: break-all;
}

.stat-card__detail {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* ── Tables Grid ───────────────────────────────────────────────────────── */
.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 1.5rem;
}

/* ── Chart Card (reused for alerts and tables) ─────────────────────────── */
.chart-card {
    background: linear-gradient(145deg, #2c1810, #1a0f0a);
    border-radius: 16px;
    border: 1px solid rgba(230, 200, 124, 0.15);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}

.chart-card:hover {
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.35);
}

.chart-card__header {
    background: rgba(26, 15, 10, 0.5);
    border-bottom: 1px solid rgba(230, 200, 124, 0.15);
    padding: 1.1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.chart-card__title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.25rem;
    color: #f0d08a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    line-height: 1.3;
}

.chart-card__title i {
    color: #e6c87c;
    font-size: 1.1rem;
}

.chart-card__badge {
    background: rgba(62, 39, 35, 0.8);
    color: #e6c87c;
    font-size: 0.75rem;
    padding: 0.3rem 0.9rem;
    border-radius: 25px;
    font-family: 'Manrope', sans-serif;
    font-weight: 500;
    white-space: nowrap;
}

.chart-card__body {
    padding: 1.5rem;
}

.chart-card__body--no-pad {
    padding: 0;
}

.chart-card__link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: linear-gradient(135deg, #e6c87c, #b8860b);
    color: #1a0f0a;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-family: 'Manrope', sans-serif;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.chart-card__link:hover,
.chart-card__link:focus-visible {
    background: linear-gradient(135deg, #f0d08a, #e6c87c);
    transform: translateY(-1px);
    color: #1a0f0a;
}

.chart-card__link:focus-visible {
    outline: 2px solid #e6c87c;
    outline-offset: 2px;
}

/* ── Data Table ────────────────────────────────────────────────────────── */
.table-responsive-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #3c2820 transparent;
}

.table-responsive-wrapper::-webkit-scrollbar {
    height: 6px;
}

.table-responsive-wrapper::-webkit-scrollbar-track {
    background: transparent;
}

.table-responsive-wrapper::-webkit-scrollbar-thumb {
    background: #3c2820;
    border-radius: 3px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    color: rgba(255, 255, 255, 0.85);
    font-family: 'Manrope', sans-serif;
    font-size: 0.88rem;
    min-width: 500px;
}

.data-table thead th {
    background: rgba(26, 15, 10, 0.6);
    color: #e6c87c;
    border-bottom: 2px solid rgba(230, 200, 124, 0.2);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0.9rem 1rem;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
}

.data-table tbody td {
    border-bottom: 1px solid rgba(230, 200, 124, 0.08);
    padding: 0.8rem 1rem;
    vertical-align: middle;
}

.data-table tbody tr {
    transition: background 0.2s ease;
}

.data-table tbody tr:hover,
.data-table tbody tr:focus-visible {
    background: rgba(230, 200, 124, 0.05);
}

.data-table .text-gold {
    color: #e6c87c !important;
}

.data-table .text-muted {
    color: rgba(255, 255, 255, 0.5) !important;
}

.data-table i.fa-coffee {
    color: #e6c87c;
    margin-right: 0.5rem;
}

/* ── Alert List ─────────────────────────────────────────────────────────── */
.alert-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.alert-list__item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1.5rem;
    border-bottom: 1px solid rgba(230, 200, 124, 0.08);
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    transition: background 0.2s ease;
}

.alert-list__item:hover {
    background: rgba(230, 200, 124, 0.05);
}

.alert-list__item:last-child {
    border-bottom: none;
}

.alert-list__item i:first-child {
    font-size: 1rem;
    min-width: 20px;
    text-align: center;
}

.alert-list__item span:first-of-type {
    flex: 1;
    min-width: 0;
    word-break: break-word;
}

/* ── Badges ────────────────────────────────────────────────────────────── */
.badge {
    display: inline-block;
    padding: 0.25rem 0.7rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    font-family: 'Manrope', sans-serif;
    white-space: nowrap;
}

.badge--info {
    background: rgba(21, 101, 192, 0.25);
    color: #64b5f6;
}

.badge--danger {
    background: rgba(198, 40, 40, 0.25);
    color: #ef9a9a;
}

.badge--warning {
    background: rgba(184, 134, 11, 0.25);
    color: #e6c87c;
}

.badge--success {
    background: rgba(46, 125, 50, 0.25);
    color: #81c784;
}

.badge--secondary {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
}

.text-danger { color: #ef9a9a !important; }
.text-warning { color: #e6c87c !important; }
.text-info { color: #64b5f6 !important; }
.text-success { color: #81c784 !important; }
.text-muted { color: rgba(255, 255, 255, 0.5) !important; }

/* ── Empty State ────────────────────────────────────────────────────────── */
.chart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
    color: rgba(255, 255, 255, 0.4);
    text-align: center;
}

.chart-empty i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    opacity: 0.5;
}

.chart-empty p {
    font-size: 0.9rem;
    margin: 0;
    font-family: 'Manrope', sans-serif;
}

/* ── Responsive: Large Desktop ─────────────────────────────────────────── */
@media (min-width: 1400px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ── Responsive: Desktop ───────────────────────────────────────────────── */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }

    .stat-card { padding: 1.25rem; }
    .stat-card__value { font-size: 1.5rem; }
    .stat-card__icon { width: 50px; height: 50px; min-width: 50px; font-size: 1.3rem; }

    .quick-access-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

/* ── Responsive: Tablet ─────────────────────────────────────────────────── */
@media (max-width: 992px) {
    .dashboard-container { padding: 1rem; }
    .stats-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
    .stat-card { padding: 1rem; }
    .stat-card__value { font-size: 1.35rem; }
    .chart-card__title { font-size: 1.1rem; }
    .tables-grid { grid-template-columns: 1fr; }

    .quick-access-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }
}

/* ── Responsive: Mobile Landscape ────────────────────────────────────────── */
@media (max-width: 768px) {
    .stat-card__value { font-size: 1.5rem; }
    .stat-card__icon { width: 48px; height: 48px; min-width: 48px; font-size: 1.2rem; }

    .chart-card__header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 1rem;
    }

    .chart-card__body { padding: 1rem; }

    .quick-access-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .section-heading { font-size: 1.2rem; }
}

/* ── Responsive: Mobile Portrait ────────────────────────────────────────── */
@media (max-width: 576px) {
    .dashboard-container { padding: 0.75rem; }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .stat-card {
        padding: 0.9rem;
        min-height: 90px;
    }

    .stat-card__icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        font-size: 1.1rem;
        margin-right: 0.75rem;
    }

    .stat-card__value { font-size: 1.3rem; }
    .stat-card__label { font-size: 0.72rem; }

    .chart-card__body { padding: 0.75rem; }

    .data-table {
        font-size: 0.82rem;
    }

    .data-table thead th,
    .data-table tbody td {
        padding: 0.65rem 0.5rem;
    }

    .alert-list__item {
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }

    .quick-access-grid {
        grid-template-columns: 1fr;
    }

    .quick-access-card {
        padding: 1rem 0.75rem;
        font-size: 0.85rem;
    }
}

/* ── Reduced Motion (accessibility) ─────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
