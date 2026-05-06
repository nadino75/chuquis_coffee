<template>
    <div>
        <!-- Loading -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando datos del dashboard...</p>
        </div>

        <!-- ══════════════ CAJERO ══════════════ -->
        <template v-else-if="rolDashboard === 'cajero'">
            <div class="row mb-3">
                <div class="col-12">
                    <router-link to="/ventas" class="btn btn-coffee mr-2">
                        <i class="fas fa-cash-register mr-1"></i> Nueva Venta
                    </router-link>
                    <router-link to="/clientes" class="btn btn-outline-coffee">
                        <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
                    </router-link>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3" v-for="(stat, key) in estadisticas" :key="key">
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

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock text-gold"></i> Ventas por Hora - Hoy</h3>
                            <span class="badge bg-coffee">Hoy</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.ventas_por_hora?.length" class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin ventas registradas hoy</p>
                            </div>
                            <canvas v-else id="chartVentasPorHora" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-credit-card text-gold"></i> Pagos Hoy</h3>
                            <span class="badge bg-coffee">Hoy</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.metodos_pago_hoy?.length" class="text-center py-4">
                                <i class="fas fa-money-bill fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin pagos hoy</p>
                            </div>
                            <canvas v-else id="chartMetodosPagoHoy" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list-alt text-gold"></i> Ventas de Hoy</h3>
                            <router-link to="/ventas" class="btn btn-sm btn-coffee float-right">
                                <i class="fas fa-list"></i> Ver Todas
                            </router-link>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" v-if="ventasHoy?.length">
                                <table class="table table-sm table-hover table-coffee">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cliente</th>
                                            <th>Pago</th>
                                            <th>Total</th>
                                            <th>Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="venta in ventasHoy" :key="venta.id" class="table-row-hover">
                                            <td><i class="fas fa-coffee text-gold mr-1"></i>{{ venta.producto_nombre || 'Varios' }}</td>
                                            <td>{{ venta.cliente?.nombres || 'Consumidor Final' }}</td>
                                            <td><span class="badge bg-info">{{ capitalize(venta.pago?.tipo_pago) || '-' }}</span></td>
                                            <td><strong class="text-gold">${{ formatNumber(venta.suma_total) }}</strong></td>
                                            <td class="text-muted">{{ formatHour(venta.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center p-5" v-else>
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay ventas registradas hoy</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle text-danger"></i> Sin Stock</h3>
                            <span class="badge bg-danger">{{ sinStock?.length || 0 }}</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" v-if="sinStock?.length">
                                <li class="list-group-item sin-stock-item" v-for="p in sinStock" :key="p.nombre">
                                    <i class="fas fa-times-circle text-danger mr-2"></i>
                                    <span class="text-white">{{ p.nombre }}</span>
                                    <span class="badge bg-danger float-right">0</span>
                                </li>
                            </ul>
                            <div class="text-center p-5" v-else>
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">Todos los productos tienen stock</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ══════════════ CONTADOR ══════════════ -->
        <template v-else-if="rolDashboard === 'contador'">
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-3" v-for="(stat, key) in estadisticas" :key="key">
                    <div class="stat-card" :style="{ borderLeftColor: getStatColor(stat.color) }">
                        <div class="stat-icon" :style="{ background: getStatGradient(stat.color) }">
                            <i :class="stat.icon"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">{{ stat.titulo }}</span>
                            <span class="stat-value" :class="key === 'variacion' && stat.total < 0 ? 'text-danger-soft' : ''">
                                <template v-if="key === 'variacion'">
                                    {{ stat.total >= 0 ? '+' : '' }}{{ stat.total }}%
                                </template>
                                <template v-else-if="key === 'transacciones_mes'">
                                    {{ formatNumber(stat.total) }}
                                </template>
                                <template v-else>
                                    ${{ formatNumber(stat.total) }}
                                </template>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar text-gold"></i> Ingresos por Mes</h3>
                            <span class="badge bg-coffee">Últimos 6 meses</span>
                        </div>
                        <div class="card-body">
                            <canvas id="chartIngresosPorMes" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-credit-card text-gold"></i> Ingresos por Método</h3>
                            <span class="badge bg-coffee">30 días</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.ingresos_por_metodo?.length" class="text-center py-4">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin datos disponibles</p>
                            </div>
                            <canvas v-else id="chartIngresosPorMetodo" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tags text-gold"></i> Ingresos por Categoría</h3>
                            <span class="badge bg-coffee">30 días</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.ingresos_por_categoria?.length" class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin datos disponibles</p>
                            </div>
                            <canvas v-else id="chartIngresosPorCategoria" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line text-gold"></i> Tendencia del Mes</h3>
                            <span class="badge bg-coffee">Mes actual</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.tendencia_mes?.length" class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin datos disponibles</p>
                            </div>
                            <canvas v-else id="chartTendenciaMes" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ══════════════ GENERAL (Admin / Gerente / default) ══════════════ -->
        <template v-else>
            <div class="row mb-4">
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3" v-for="(stat, key) in estadisticas" :key="key">
                    <div class="stat-card" :style="{ borderLeftColor: getStatColor(stat.color) }">
                        <div class="stat-icon" :style="{ background: getStatGradient(stat.color) }">
                            <i :class="stat.icon"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">{{ stat.titulo }}</span>
                            <span class="stat-value">
                                <template v-if="stat.ingresos !== undefined">
                                    ${{ formatNumber(stat.ingresos ?? stat.total) }}
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

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line text-gold"></i> Ventas - Últimos 7 Días</h3>
                            <span class="badge bg-coffee">7 días</span>
                        </div>
                        <div class="card-body">
                            <canvas id="chartVentas7Dias" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-credit-card text-gold"></i> Métodos de Pago</h3>
                            <span class="badge bg-coffee">30 días</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.metodos_pago?.length" class="text-center py-4">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin datos de pagos</p>
                            </div>
                            <canvas v-else id="chartMetodosPago" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-star text-gold"></i> Productos Más Vendidos</h3>
                            <span class="badge bg-coffee">Top 8 — 30 días</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.productos_mas_vendidos?.length" class="text-center py-4">
                                <i class="fas fa-box fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin datos de ventas</p>
                            </div>
                            <canvas v-else id="chartProductosVendidos" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tags text-gold"></i> Ventas por Categoría</h3>
                            <span class="badge bg-coffee">30 días</span>
                        </div>
                        <div class="card-body">
                            <div v-if="!datosGraficos.ventas_por_categoria?.length" class="text-center py-4">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-2"></i>
                                <p class="text-muted">Sin datos de categorías</p>
                            </div>
                            <canvas v-else id="chartVentasCategoria" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

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
                                <p class="text-muted">No hay alertas en este momento</p>
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
                                            <td>{{ venta.cliente?.nombres || 'N/A' }}</td>
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
import { ref, nextTick, onMounted, onUnmounted } from 'vue';
import Chart from 'chart.js/auto';
import dashboardService from '@/services/dashboard';

const estadisticas   = ref({});
const datosGraficos  = ref({});
const alertas        = ref([]);
const ventasRecientes = ref([]);
const ventasHoy      = ref([]);
const sinStock       = ref([]);
const rolDashboard   = ref('');
const loading        = ref(true);
let charts = [];
let refreshInterval = null;

// ── Palette ──────────────────────────────────────────────────────────────────

const coffeeColors = ['#8B4513','#A0522D','#D4A742','#B8860B','#DAA520','#DEB887','#F0D08A','#CD853F'];

const colorMap = {
    primary: { solid: '#8B4513', gradient: 'linear-gradient(135deg,#8B4513,#A0522D)' },
    success: { solid: '#2E7D32', gradient: 'linear-gradient(135deg,#2E7D32,#388E3C)' },
    warning: { solid: '#B8860B', gradient: 'linear-gradient(135deg,#B8860B,#DAA520)' },
    danger:  { solid: '#C62828', gradient: 'linear-gradient(135deg,#C62828,#D32F2F)' },
    info:    { solid: '#1565C0', gradient: 'linear-gradient(135deg,#1565C0,#1976D2)' },
};

const tooltipBase = {
    backgroundColor: 'rgba(26,15,10,0.9)',
    titleColor: '#F0D08A',
    bodyColor: '#ffffff',
    borderColor: '#D4A742',
    borderWidth: 1,
    padding: 12,
};

// ── Helpers ───────────────────────────────────────────────────────────────────

function getStatColor(color)    { return colorMap[color]?.solid    || '#8B4513'; }
function getStatGradient(color) { return colorMap[color]?.gradient || 'linear-gradient(135deg,#8B4513,#A0522D)'; }
function getBadgeClass(tipo)    { return { danger:'bg-danger', warning:'bg-warning', info:'bg-info', success:'bg-success' }[tipo] || 'bg-secondary'; }
function getTextColor(tipo)     { return { danger:'text-danger', warning:'text-warning', info:'text-info', success:'text-success' }[tipo] || 'text-muted'; }

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatNumber(num) {
    return Number(num || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' }) + ' ' +
           d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

function formatHour(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

// ── Chart renderers ───────────────────────────────────────────────────────────

function renderCharts() {
    charts.forEach(c => c.destroy());
    charts = [];

    if (rolDashboard.value === 'cajero')   renderCajeroCharts();
    else if (rolDashboard.value === 'contador') renderContadorCharts();
    else renderGeneralCharts();
}

function renderCajeroCharts() {
    const data = datosGraficos.value;

    // Bar – ventas por hora
    const ventasPorHora = data.ventas_por_hora || [];
    if (ventasPorHora.length) {
        const el = document.getElementById('chartVentasPorHora');
        if (el) {
            const horaMap = {};
            ventasPorHora.forEach(v => { horaMap[v.hora] = v.cantidad; });
            const horas = Array.from({ length: 24 }, (_, i) => i);
            charts.push(new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: horas.map(h => String(h).padStart(2, '0') + ':00'),
                    datasets: [{
                        label: 'Ventas',
                        data: horas.map(h => horaMap[h] || 0),
                        backgroundColor: 'rgba(212,167,66,0.6)',
                        borderColor: '#D4A742',
                        borderWidth: 2,
                        borderRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.1)' } },
                        x: { ticks: { color: '#DEB887', maxRotation: 45, minRotation: 45 }, grid: { color: 'rgba(212,167,66,0.05)' } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ventas: ' + ctx.parsed.y } },
                    },
                },
            }));
        }
    }

    // Doughnut – métodos de pago hoy
    const metodosPagoHoy = data.metodos_pago_hoy || [];
    if (metodosPagoHoy.length) {
        const el = document.getElementById('chartMetodosPagoHoy');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: metodosPagoHoy.map(m => capitalize(m.tipo_pago)),
                    datasets: [{
                        data: metodosPagoHoy.map(m => m.cantidad),
                        backgroundColor: coffeeColors,
                        borderColor: '#1a0f0a',
                        borderWidth: 2,
                        hoverOffset: 15,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15 } },
                        tooltip: {
                            ...tooltipBase,
                            callbacks: {
                                label(ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                    return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                                },
                            },
                        },
                    },
                },
            }));
        }
    }
}

function renderContadorCharts() {
    const data = datosGraficos.value;

    // Bar – ingresos por mes (últimos 6 meses)
    const ingresosPorMes = data.ingresos_por_mes || [];
    const elMes = document.getElementById('chartIngresosPorMes');
    if (elMes) {
        charts.push(new Chart(elMes.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ingresosPorMes.map(m => m.mes),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: ingresosPorMes.map(m => m.total),
                    backgroundColor: 'rgba(139,69,19,0.7)',
                    borderColor: '#D4A742',
                    borderWidth: 2,
                    borderRadius: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString(), color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.1)' } },
                    x: { ticks: { color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.05)' } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ingresos: $' + ctx.parsed.y.toLocaleString() } },
                },
            },
        }));
    }

    // Doughnut – ingresos por método de pago ($)
    const ingresosPorMetodo = data.ingresos_por_metodo || [];
    if (ingresosPorMetodo.length) {
        const el = document.getElementById('chartIngresosPorMetodo');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ingresosPorMetodo.map(m => capitalize(m.tipo_pago)),
                    datasets: [{
                        data: ingresosPorMetodo.map(m => Number(m.monto_total || 0)),
                        backgroundColor: coffeeColors,
                        borderColor: '#1a0f0a',
                        borderWidth: 2,
                        hoverOffset: 15,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15 } },
                        tooltip: {
                            ...tooltipBase,
                            callbacks: {
                                label(ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : '0.0';
                                    return ctx.label + ': $' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                                },
                            },
                        },
                    },
                },
            }));
        }
    }

    // Horizontal bar – ingresos por categoría
    const ingresosPorCategoria = data.ingresos_por_categoria || [];
    if (ingresosPorCategoria.length) {
        const el = document.getElementById('chartIngresosPorCategoria');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ingresosPorCategoria.map(c => c.categoria || 'Sin categoría'),
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: ingresosPorCategoria.map(c => Number(c.total || 0)),
                        backgroundColor: 'rgba(212,167,66,0.6)',
                        borderColor: '#D4A742',
                        borderWidth: 2,
                        borderRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString(), color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.1)' } },
                        y: { ticks: { color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.05)' } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipBase, callbacks: { label: ctx => '$' + ctx.parsed.x.toLocaleString() } },
                    },
                },
            }));
        }
    }

    // Line – tendencia diaria del mes
    const tendenciaMes = data.tendencia_mes || [];
    if (tendenciaMes.length) {
        const el = document.getElementById('chartTendenciaMes');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'line',
                data: {
                    labels: tendenciaMes.map(v => {
                        const d = new Date(v.fecha);
                        return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
                    }),
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: tendenciaMes.map(v => Number(v.total || 0)),
                        borderColor: '#D4A742',
                        backgroundColor: 'rgba(212,167,66,0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#D4A742',
                        pointBorderColor: '#1a0f0a',
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString(), color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.1)' } },
                        x: { ticks: { color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.05)' } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ingresos: $' + ctx.parsed.y.toLocaleString() } },
                    },
                },
            }));
        }
    }
}

function renderGeneralCharts() {
    const data = datosGraficos.value;

    // Line – ventas 7 días
    const ventas7Dias = data.ventas_7_dias || [];
    const elVentas = document.getElementById('chartVentas7Dias');
    if (elVentas) {
        charts.push(new Chart(elVentas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ventas7Dias.map(v => {
                    const d = new Date(v.fecha);
                    return d.toLocaleDateString('es-ES', { weekday: 'short', day: '2-digit' });
                }),
                datasets: [{
                    label: 'Ventas ($)',
                    data: ventas7Dias.map(v => v.total || 0),
                    borderColor: '#D4A742',
                    backgroundColor: 'rgba(212,167,66,0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#D4A742',
                    pointBorderColor: '#1a0f0a',
                    pointBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString(), color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.1)' } },
                    x: { ticks: { color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.05)' } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ventas: $' + ctx.parsed.y.toLocaleString() } },
                },
            },
        }));
    }

    // Doughnut – métodos de pago
    const metodosPago = data.metodos_pago || [];
    if (metodosPago.length) {
        const el = document.getElementById('chartMetodosPago');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: metodosPago.map(m => capitalize(m.tipo_pago)),
                    datasets: [{
                        data: metodosPago.map(m => m.cantidad),
                        backgroundColor: coffeeColors,
                        borderColor: '#1a0f0a',
                        borderWidth: 2,
                        hoverOffset: 15,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15 } },
                        tooltip: {
                            ...tooltipBase,
                            callbacks: {
                                label(ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                    return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                                },
                            },
                        },
                    },
                },
            }));
        }
    }

    // Horizontal bar – productos más vendidos
    const productosVendidos = (data.productos_mas_vendidos || []).slice(0, 8);
    if (productosVendidos.length) {
        const el = document.getElementById('chartProductosVendidos');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: productosVendidos.map(p => (p.nombre || 'Producto').length > 20 ? (p.nombre || 'Producto').substring(0, 20) + '…' : (p.nombre || 'Producto')),
                    datasets: [{
                        label: 'Cantidad vendida',
                        data: productosVendidos.map(p => p.cantidad_vendida),
                        backgroundColor: 'rgba(139,69,19,0.7)',
                        borderColor: '#8B4513',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.1)' } },
                        y: { ticks: { color: '#DEB887' }, grid: { color: 'rgba(212,167,66,0.05)' } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Vendidos: ' + ctx.parsed.x + ' unidades' } },
                    },
                },
            }));
        }
    }

    // Pie – ventas por categoría
    const ventasCategoria = data.ventas_por_categoria || [];
    if (ventasCategoria.length) {
        const el = document.getElementById('chartVentasCategoria');
        if (el) {
            charts.push(new Chart(el.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ventasCategoria.map(v => v.categoria || 'Sin categoría'),
                    datasets: [{
                        data: ventasCategoria.map(v => v.total || 0),
                        backgroundColor: coffeeColors,
                        borderColor: '#1a0f0a',
                        borderWidth: 2,
                        hoverOffset: 15,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15 } },
                        tooltip: {
                            ...tooltipBase,
                            callbacks: {
                                label(ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : '0.0';
                                    return ctx.label + ': $' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                                },
                            },
                        },
                    },
                },
            }));
        }
    }
}

// ── Data loading ──────────────────────────────────────────────────────────────

async function loadDashboard() {
    try {
        const response = await dashboardService.getDatosDashboard();
        const data = response.data;
        if (data.success) {
            rolDashboard.value   = data.rol || 'general';
            estadisticas.value   = data.estadisticas  || {};
            datosGraficos.value  = data.datosGraficos  || {};
            alertas.value        = data.alertas        || [];
            ventasRecientes.value = data.ventasRecientes || [];
            ventasHoy.value      = data.ventasHoy      || [];
            sinStock.value       = data.sinStock       || [];

            loading.value = false;   // 1. hide spinner so template renders canvases
            await nextTick();        // 2. wait for DOM update
            renderCharts();          // 3. canvases are now in DOM
        }
    } catch (e) {
        console.error('Error cargando dashboard:', e);
        loading.value = false;
    }
}

onMounted(() => {
    loadDashboard();
    refreshInterval = setInterval(loadDashboard, 120000);
});

onUnmounted(() => {
    if (refreshInterval) clearInterval(refreshInterval);
    charts.forEach(c => c.destroy());
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

.text-danger-soft { color: #ef9a9a !important; }

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

.btn-coffee {
    background: linear-gradient(135deg, #e6c87c, #b8860b);
    border: none; color: #1a0f0a; font-weight: 600;
}
.btn-coffee:hover {
    background: linear-gradient(135deg, #f0d08a, #e6c87c);
    color: #1a0f0a; transform: translateY(-1px);
}

.btn-outline-coffee {
    background: transparent;
    border: 2px solid #e6c87c;
    color: #e6c87c; font-weight: 600;
}
.btn-outline-coffee:hover {
    background: rgba(230,200,124,0.1);
    color: #f0d08a;
}

.alert-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(230,200,124,0.1);
    transition: background 0.3s ease;
}
.alert-item:hover { background: rgba(230,200,124,0.05); }
.alert-item .product-title { color: rgba(255,255,255,0.8); font-size: 0.95rem; }
.alert-item .product-title:hover { color: #f0d08a; }

.sin-stock-item {
    background: #1a0f0a !important;
    border-color: rgba(230,200,124,0.1) !important;
    padding: 0.75rem 1.25rem;
}

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
