<template>
    <div class="dashboard-container" role="main" aria-label="Panel de control principal">
        <a href="#dashboard-content" class="skip-link">Saltar al contenido principal</a>

        <!-- Loading state with accessible announcement -->
        <div v-if="loading" class="loading-state" role="status" aria-live="polite">
            <div class="spinner" aria-hidden="true">
                <div class="spinner-ring"></div>
            </div>
            <p class="loading-text">Cargando datos del dashboard...</p>
        </div>

        <div id="dashboard-content" v-else>
            <!-- ══════════════ CAJERO ══════════════ -->
            <template v-if="rolDashboard === 'cajero'">
                <header class="dashboard-header">
                    <h1 class="sr-only">Dashboard Cajero</h1>
                    <nav class="quick-actions" aria-label="Acciones rápidas">
                        <router-link to="/ventas" class="action-btn action-btn--primary">
                            <i class="fas fa-cash-register" aria-hidden="true"></i>
                            <span>Nueva Venta</span>
                        </router-link>
                        <router-link to="/clientes" class="action-btn action-btn--outline">
                            <i class="fas fa-user-plus" aria-hidden="true"></i>
                            <span>Nuevo Cliente</span>
                        </router-link>
                    </nav>
                </header>

                <!-- Stats with aria-live for dynamic updates -->
                <section class="stats-grid" aria-label="Estadísticas del día" aria-live="polite">
                    <article
                        v-for="(stat, key) in estadisticas"
                        :key="key"
                        class="stat-card"
                        :style="{ borderLeftColor: getStatColor(stat.color) }"
                        role="img"
                        :aria-label="`${stat.titulo}: ${stat.ingresos !== undefined ? 'Bs. ' + formatNumber(stat.ingresos) : formatNumber(stat.total)}`"
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

                <!-- Charts Row 1 -->
                <div class="charts-grid">
                    <section class="chart-card" aria-label="Ventas por hora">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-clock" aria-hidden="true"></i> Ventas por Hora - Hoy
                            </h2>
                            <span class="chart-card__badge">Hoy</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.ventas_por_hora?.length" class="chart-empty" role="img" aria-label="Sin datos de ventas hoy">
                                <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                <p>Sin ventas registradas hoy</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 250px;">
                                <canvas id="chartVentasPorHora" role="img" aria-label="Gráfico de barras: ventas por hora del día actual"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="chart-card" aria-label="Métodos de pago">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-credit-card" aria-hidden="true"></i> Pagos Hoy
                            </h2>
                            <span class="chart-card__badge">Hoy</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.metodos_pago_hoy?.length" class="chart-empty" role="img" aria-label="Sin pagos registrados hoy">
                                <i class="fas fa-money-bill" aria-hidden="true"></i>
                                <p>Sin pagos hoy</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 250px;">
                                <canvas id="chartMetodosPagoHoy" role="img" aria-label="Gráfico circular: métodos de pago utilizados hoy"></canvas>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Tables Row -->
                <div class="tables-grid">
                    <section class="chart-card" aria-label="Ventas de hoy">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-list-alt" aria-hidden="true"></i> Ventas de Hoy
                            </h2>
                            <router-link to="/ventas" class="chart-card__link">
                                <i class="fas fa-list" aria-hidden="true"></i> Ver Todas
                            </router-link>
                        </div>
                        <div class="chart-card__body chart-card__body--no-pad">
                            <div class="table-responsive-wrapper">
                                <table class="data-table" aria-label="Lista de ventas realizadas hoy">
                                    <caption class="sr-only">Ventas registradas el día de hoy con detalles de producto, cliente, método de pago y total</caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">Producto</th>
                                            <th scope="col">Cliente</th>
                                            <th scope="col">Pago</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="venta in ventasHoy" :key="venta.id">
                                            <td><i class="fas fa-coffee" aria-hidden="true"></i> {{ venta.producto_nombre || 'Varios' }}</td>
                                            <td>{{ venta.cliente?.nombres || 'Consumidor Final' }}</td>
                                            <td><span class="badge badge--info">{{ capitalize(venta.pago?.tipo_pago) || '-' }}</span></td>
                                            <td class="text-gold"><strong>Bs. {{ formatNumber(venta.suma_total) }}</strong></td>
                                            <td class="text-muted">{{ formatHour(venta.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="!ventasHoy?.length" class="chart-empty" role="img" aria-label="Sin ventas hoy">
                                <i class="fas fa-receipt" aria-hidden="true"></i>
                                <p>No hay ventas registradas hoy</p>
                            </div>
                        </div>
                    </section>

                    <section class="chart-card" aria-label="Productos sin stock">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Sin Stock
                            </h2>
                            <span class="chart-card__badge chart-card__badge--danger">{{ sinStock?.length || 0 }}</span>
                        </div>
                        <div class="chart-card__body chart-card__body--no-pad">
                            <ul v-if="sinStock?.length" class="alert-list" aria-label="Lista de productos sin stock disponible">
                                <li v-for="p in sinStock" :key="p.nombre" class="alert-list__item">
                                    <i class="fas fa-times-circle text-danger" aria-hidden="true"></i>
                                    <span>{{ p.nombre }}</span>
                                    <span class="badge badge--danger">0</span>
                                </li>
                            </ul>
                            <div v-else class="chart-empty" role="img" aria-label="Todos los productos tienen stock disponible">
                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                <p>Todos los productos tienen stock</p>
                            </div>
                        </div>
                    </section>
                </div>
            </template>

            <!-- ══════════════ CONTADOR ══════════════ -->
            <template v-else-if="rolDashboard === 'contador'">
                <h1 class="sr-only">Dashboard Contador</h1>

                <section class="stats-grid" aria-label="Estadísticas financieras" aria-live="polite">
                    <article
                        v-for="(stat, key) in estadisticas"
                        :key="key"
                        class="stat-card"
                        :style="{ borderLeftColor: getStatColor(stat.color) }"
                        role="img"
                        :aria-label="`${stat.titulo}: ${key === 'variacion' ? stat.total + '%' : key === 'transacciones_mes' ? formatNumber(stat.total) : 'Bs. ' + formatNumber(stat.total)}`"
                    >
                        <div class="stat-card__icon" :style="{ background: getStatGradient(stat.color) }" aria-hidden="true">
                            <i :class="stat.icon"></i>
                        </div>
                        <div class="stat-card__content">
                            <span class="stat-card__label">{{ stat.titulo }}</span>
                            <span class="stat-card__value" :class="{ 'text-danger-soft': key === 'variacion' && stat.total < 0 }">
                                <template v-if="key === 'variacion'">{{ stat.total >= 0 ? '+' : '' }}{{ stat.total }}%</template>
                                <template v-else-if="key === 'transacciones_mes'">{{ formatNumber(stat.total) }}</template>
                                <template v-else>Bs. {{ formatNumber(stat.total) }}</template>
                            </span>
                        </div>
                    </article>
                </section>

                <div class="charts-grid">
                    <section class="chart-card" aria-label="Ingresos por mes">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-chart-bar" aria-hidden="true"></i> Ingresos por Mes
                            </h2>
                            <span class="chart-card__badge">Últimos 6 meses</span>
                        </div>
                        <div class="chart-card__body">
                            <div class="chart-wrapper" style="position: relative; height: 250px;">
                                <canvas id="chartIngresosPorMes" role="img" aria-label="Gráfico de barras: ingresos por mes en los últimos 6 meses"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="chart-card" aria-label="Ingresos por método de pago">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-credit-card" aria-hidden="true"></i> Ingresos por Método
                            </h2>
                            <span class="chart-card__badge">30 días</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.ingresos_por_metodo?.length" class="chart-empty" role="img" aria-label="Sin datos de métodos de pago">
                                <i class="fas fa-chart-pie" aria-hidden="true"></i>
                                <p>Sin datos disponibles</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 250px;">
                                <canvas id="chartIngresosPorMetodo" role="img" aria-label="Gráfico circular: ingresos distribuidos por método de pago"></canvas>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="charts-grid">
                    <section class="chart-card" aria-label="Ingresos por categoría">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-tags" aria-hidden="true"></i> Ingresos por Categoría
                            </h2>
                            <span class="chart-card__badge">30 días</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.ingresos_por_categoria?.length" class="chart-empty" role="img" aria-label="Sin datos de categorías">
                                <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                <p>Sin datos disponibles</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 300px;">
                                <canvas id="chartIngresosPorCategoria" role="img" aria-label="Gráfico de barras horizontal: ingresos por categoría de producto"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="chart-card" aria-label="Tendencia del mes">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-chart-line" aria-hidden="true"></i> Tendencia del Mes
                            </h2>
                            <span class="chart-card__badge">Mes actual</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.tendencia_mes?.length" class="chart-empty" role="img" aria-label="Sin datos de tendencia mensual">
                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                                <p>Sin datos disponibles</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 300px;">
                                <canvas id="chartTendenciaMes" role="img" aria-label="Gráfico de línea: tendencia de ingresos durante el mes actual"></canvas>
                            </div>
                        </div>
                    </section>
                </div>
            </template>

            <!-- ══════════════ GENERAL (Admin / Gerente) ══════════════ -->
            <template v-else>
                <h1 class="sr-only">Dashboard General</h1>

                <section class="stats-grid" aria-label="Estadísticas generales" aria-live="polite">
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
                                <template v-if="stat.ingresos !== undefined">Bs. {{ formatNumber(stat.ingresos ?? stat.total) }}</template>
                                <template v-else>{{ formatNumber(stat.total) }}</template>
                            </span>
                            <small v-if="stat.ingresos !== undefined" class="stat-card__detail">
                                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                                {{ formatNumber(stat.total) }} ventas
                            </small>
                        </div>
                    </article>
                </section>

                <div class="charts-grid">
                    <section class="chart-card" aria-label="Ventas últimos 7 días">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-chart-line" aria-hidden="true"></i> Ventas - Últimos 7 Días
                            </h2>
                            <span class="chart-card__badge">7 días</span>
                        </div>
                        <div class="chart-card__body">
                            <div class="chart-wrapper" style="position: relative; height: 250px;">
                                <canvas id="chartVentas7Dias" role="img" aria-label="Gráfico de línea: ventas totales en los últimos 7 días"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="chart-card" aria-label="Métodos de pago">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-credit-card" aria-hidden="true"></i> Métodos de Pago
                            </h2>
                            <span class="chart-card__badge">30 días</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.metodos_pago?.length" class="chart-empty" role="img" aria-label="Sin datos de métodos de pago">
                                <i class="fas fa-chart-pie" aria-hidden="true"></i>
                                <p>Sin datos de pagos</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 250px;">
                                <canvas id="chartMetodosPago" role="img" aria-label="Gráfico circular: distribución de métodos de pago en los últimos 30 días"></canvas>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="charts-grid">
                    <section class="chart-card" aria-label="Productos más vendidos">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-star" aria-hidden="true"></i> Productos Más Vendidos
                            </h2>
                            <span class="chart-card__badge">Top 8 — 30 días</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.productos_mas_vendidos?.length" class="chart-empty" role="img" aria-label="Sin datos de productos vendidos">
                                <i class="fas fa-box" aria-hidden="true"></i>
                                <p>Sin datos de ventas</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 300px;">
                                <canvas id="chartProductosVendidos" role="img" aria-label="Gráfico de barras horizontal: top 8 productos más vendidos en 30 días"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="chart-card" aria-label="Ventas por categoría">
                        <div class="chart-card__header">
                            <h2 class="chart-card__title">
                                <i class="fas fa-tags" aria-hidden="true"></i> Ventas por Categoría
                            </h2>
                            <span class="chart-card__badge">30 días</span>
                        </div>
                        <div class="chart-card__body">
                            <div v-if="!datosGraficos.ventas_por_categoria?.length" class="chart-empty" role="img" aria-label="Sin datos de ventas por categoría">
                                <i class="fas fa-chart-pie" aria-hidden="true"></i>
                                <p>Sin datos de categorías</p>
                            </div>
                            <div v-else class="chart-wrapper" style="position: relative; height: 300px;">
                                <canvas id="chartVentasCategoria" role="img" aria-label="Gráfico circular: ventas distribuidas por categoría de producto"></canvas>
                            </div>
                        </div>
                    </section>
                </div>

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
                                <p>No hay alertas en este momento</p>
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
                                            <td>{{ venta.cliente?.nombres || 'N/A' }}</td>
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
            </template>
        </div>
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

// ── Palette (ISO-compliant contrast ratios) ───────────────────────────────

const coffeeColors = ['#8B4513','#A0522D','#D4A742','#B8860B','#DAA520','#DEB887','#F0D08A','#CD853F'];

const colorMap = {
    primary: { solid: '#8B4513', gradient: 'linear-gradient(135deg,#8B4513,#A0522D)' },
    success: { solid: '#2E7D32', gradient: 'linear-gradient(135deg,#2E7D32,#388E3C)' },
    warning: { solid: '#B8860B', gradient: 'linear-gradient(135deg,#B8860B,#DAA520)' },
    danger:  { solid: '#C62828', gradient: 'linear-gradient(135deg,#C62828,#D32F2F)' },
    info:    { solid: '#1565C0', gradient: 'linear-gradient(135deg,#1565C0,#1976D2)' },
};

const currencySymbol = 'Bs.';

const tooltipBase = {
    backgroundColor: 'rgba(26,15,10,0.95)',
    titleColor: '#F0D08A',
    bodyColor: '#ffffff',
    borderColor: '#D4A742',
    borderWidth: 2,
    padding: 14,
    cornerRadius: 8,
    titleFont: { family: "'Cormorant Garamond', serif", size: 14, weight: '600' },
    bodyFont: { family: "'Manrope', sans-serif", size: 13 },
};

// ── Helpers ───────────────────────────────────────────────────────────────

function getStatColor(color)    { return colorMap[color]?.solid    || '#8B4513'; }
function getStatGradient(color) { return colorMap[color]?.gradient || 'linear-gradient(135deg,#8B4513,#A0522D)'; }
function getBadgeClass(tipo)    { return { danger:'badge--danger', warning:'badge--warning', info:'badge--info', success:'badge--success' }[tipo] || 'badge--secondary'; }
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

// ── Chart renderers ───────────────────────────────────────────────────────

function renderCharts() {
    charts.forEach(c => { try { c.destroy(); } catch(e) {} });
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
                        backgroundColor: 'rgba(212,167,66,0.7)',
                        borderColor: '#D4A742',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: '#D4A742',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, color: '#DEB887', font: { size: 11 } }, grid: { color: 'rgba(212,167,66,0.08)' } },
                        x: { ticks: { color: '#DEB887', maxRotation: 45, minRotation: 45, font: { size: 10 } }, grid: { display: false } },
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
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15, font: { family: "'Manrope', sans-serif", size: 12 } } },
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

    // Bar – ingresos por mes
    const ingresosPorMes = data.ingresos_por_mes || [];
    const elMes = document.getElementById('chartIngresosPorMes');
    if (elMes) {
        charts.push(new Chart(elMes.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ingresosPorMes.map(m => m.mes),
                datasets: [{
                    label: 'Ingresos (Bs.)',
                    data: ingresosPorMes.map(m => m.total),
                    backgroundColor: 'rgba(139,69,19,0.75)',
                    borderColor: '#D4A742',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#D4A742',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v.toLocaleString(), color: '#DEB887', font: { size: 11 } }, grid: { color: 'rgba(212,167,66,0.08)' } },
                    x: { ticks: { color: '#DEB887', font: { size: 11 } }, grid: { display: false } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ingresos: Bs. ' + ctx.parsed.y.toLocaleString() } },
                },
            },
        }));
    }

    // Doughnut – ingresos por método
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
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15, font: { family: "'Manrope', sans-serif", size: 12 } } },
                        tooltip: Object.assign({}, tooltipBase, {
                             callbacks: {
                                 label(ctx) {
                                     const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                     const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : '0.0';
                                 return ctx.label + ': Bs. ' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                                  },
                              },
                          }),
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
                        label: 'Ingresos (Bs.)',
                        data: ingresosPorCategoria.map(c => Number(c.total || 0)),
                        backgroundColor: 'rgba(212,167,66,0.7)',
                        borderColor: '#D4A742',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: '#D4A742',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v.toLocaleString(), color: '#DEB887', font: { size: 11 } }, grid: { color: 'rgba(212,167,66,0.08)' } },
                        y: { ticks: { color: '#DEB887', font: { size: 11 } }, grid: { display: false } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Bs. ' + ctx.parsed.x.toLocaleString() } },
                    },
                },
            }));
        }
    }

    // Line – tendencia diaria
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
                        label: 'Ingresos (Bs.)',
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
                        y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v.toLocaleString(), color: '#DEB887', font: { size: 11 } }, grid: { color: 'rgba(212,167,66,0.08)' } },
                        x: { ticks: { color: '#DEB887', font: { size: 10 } }, grid: { display: false } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ingresos: Bs. ' + ctx.parsed.y.toLocaleString() } },
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
                    label: 'Ventas (Bs.)',
                    data: ventas7Dias.map(v => v.total || 0),
                    borderColor: '#D4A742',
                    backgroundColor: 'rgba(212,167,66,0.12)',
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
                    y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v.toLocaleString(), color: '#DEB887', font: { size: 11 } }, grid: { color: 'rgba(212,167,66,0.08)' } },
                    x: { ticks: { color: '#DEB887', font: { size: 10 } }, grid: { display: false } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipBase, callbacks: { label: ctx => 'Ventas: Bs. ' + ctx.parsed.y.toLocaleString() } },
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
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15, font: { family: "'Manrope', sans-serif", size: 12 } } },
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
                    labels: productosVendidos.map(p => (p.nombre || 'Producto').length > 18 ? (p.nombre || 'Producto').substring(0, 18) + '…' : (p.nombre || 'Producto')),
                    datasets: [{
                        label: 'Cantidad vendida',
                        data: productosVendidos.map(p => p.cantidad_vendida),
                        backgroundColor: 'rgba(139,69,19,0.75)',
                        borderColor: '#8B4513',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: '#D4A742',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { color: '#DEB887', font: { size: 11 } }, grid: { color: 'rgba(212,167,66,0.08)' } },
                        y: { ticks: { color: '#DEB887', font: { size: 11 } }, grid: { display: false } },
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
                        legend: { position: 'bottom', labels: { color: '#F0D08A', padding: 15, font: { family: "'Manrope', sans-serif", size: 12 } } },
                        tooltip: {
                            ...tooltipBase,
                            callbacks: {
                                 label(ctx) {
                                     const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                     const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : '0.0';
                                     return ctx.label + ': Bs. ' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                                 },
                            },
                        },
                    },
                },
            }));
        }
    }
}

// ── Data loading ──────────────────────────────────────────────────────────

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
            sinStock.value       = data.sinStock       || {};
            loading.value = false;
            await nextTick();
            renderCharts();
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
    charts.forEach(c => { try { c.destroy(); } catch(e) {} });
});
</script>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════════════
   DASHBOARD STYLES — ISO 9241 Compliant + Responsive + Refined Aesthetics
   ═══════════════════════════════════════════════════════════════════════════ */

/* ── Layout ──────────────────────────────────────────────────────────────── */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem;
    min-height: 100vh;
}

.dashboard-header {
    margin-bottom: 2rem;
}

/* ── Skip Link (accessibility) ──────────────────────────────────────────── */
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

/* ── Screen reader only ─────────────────────────────────────────────────── */
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

/* ── Loading State ──────────────────────────────────────────────────────── */
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

/* ── Quick Actions ─────────────────────────────────────────────────────── */
.quick-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
}

.action-btn--primary {
    background: linear-gradient(135deg, #e6c87c, #b8860b);
    color: #1a0f0a;
    box-shadow: 0 4px 15px rgba(230, 200, 124, 0.25);
}

.action-btn--primary:hover,
.action-btn--primary:focus-visible {
    background: linear-gradient(135deg, #f0d08a, #e6c87c);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(230, 200, 124, 0.35);
    color: #1a0f0a;
}

.action-btn--outline {
    background: transparent;
    border: 2px solid rgba(230, 200, 124, 0.4);
    color: #e6c87c;
}

.action-btn--outline:hover,
.action-btn--outline:focus-visible {
    background: rgba(230, 200, 124, 0.08);
    border-color: #e6c87c;
    color: #f0d08a;
    transform: translateY(-2px);
}

.action-btn:focus-visible {
    outline: 2px solid #e6c87c;
    outline-offset: 2px;
}

/* ── Stats Grid ─────────────────────────────────────────────────────────── */
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

.text-danger-soft { color: #ef9a9a !important; }

.stat-card__detail {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* ── Charts Grid ────────────────────────────────────────────────────────── */
.charts-grid,
.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

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

.chart-card__badge--danger {
    background: rgba(198, 40, 40, 0.3);
    color: #ef9a9a;
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

.chart-wrapper {
    width: 100%;
}

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

/* ── Data Table ─────────────────────────────────────────────────────────── */
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

/* ── Badges ─────────────────────────────────────────────────────────────── */
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

/* ── Responsive: Large Desktop ─────────────────────────────────────────── */
@media (min-width: 1400px) {
    .stats-grid {
        grid-template-columns: repeat(6, 1fr);
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
}

/* ── Responsive: Tablet ─────────────────────────────────────────────────── */
@media (max-width: 992px) {
    .dashboard-container { padding: 1rem; }
    .stats-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
    .stat-card { padding: 1rem; }
    .stat-card__value { font-size: 1.35rem; }
    .chart-card__title { font-size: 1.1rem; }
    .charts-grid,
    .tables-grid { grid-template-columns: 1fr; }
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

    .quick-actions { flex-direction: column; }
    .action-btn { justify-content: center; }
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
