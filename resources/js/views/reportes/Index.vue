<template>
    <div>
        <div v-if="success" class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ success }}
            <button type="button" class="close" @click="success = ''">&times;</button>
        </div>
        <div v-if="error" class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ error }}
            <button type="button" class="close" @click="error = ''">&times;</button>
        </div>

        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3" v-for="(box, key) in statBoxes" :key="key">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon" :class="box.color"><i :class="box.icon"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ box.label }}</span>
                        <span class="info-box-number">{{ box.value }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary d-flex align-items-center">
                <h5 class="card-title mb-0 text-white flex-grow-1">
                    <i class="fas fa-chart-bar mr-1"></i> Reportes
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Tipo Reporte</label>
                        <select class="form-control" v-model="filters.tipo_reporte">
                            <option value="dashboard">Dashboard</option>
                            <option value="ventas">Ventas</option>
                            <option value="pagos">Pagos</option>
                            <option value="productos">Productos</option>
                            <option value="inventario">Inventario</option>
                            <option value="clientes">Clientes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Fecha Inicio</label>
                        <input type="date" v-model="filters.fecha_inicio" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Fecha Fin</label>
                        <input type="date" v-model="filters.fecha_fin" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-block" @click="generarReporte" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                            <i class="fas fa-file-alt mr-1"></i> Generar
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-danger btn-block" @click="descargarPdf" :disabled="loadingPdf || !reporteData">
                            <span v-if="loadingPdf" class="spinner-border spinner-border-sm mr-1"></span>
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </button>
                    </div>
                </div>

                <!-- Dashboard -->
                <template v-if="filters.tipo_reporte === 'dashboard' && reporteData">
                    <div v-if="reporteData.ventas_ultima_semana?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-chart-line mr-1"></i> Ventas por Día</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>Fecha</th><th>Cantidad</th><th>Total</th></tr></thead>
                                <tbody>
                                    <tr v-for="v in reporteData.ventas_ultima_semana" :key="v.fecha" class="text-center">
                                        <td>{{ formatDate(v.fecha) }}</td>
                                        <td>{{ v.cantidad }}</td>
                                        <td>Bs. {{ formatNumber(v.total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="reporteData.productos_mas_vendidos?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-fire mr-1"></i> Productos Más Vendidos</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>#</th><th>Producto</th><th>Cantidad</th><th>Total Ingresos</th></tr></thead>
                                <tbody>
                                    <tr v-for="(p, i) in reporteData.productos_mas_vendidos" :key="p.id" class="text-center">
                                        <td>{{ i + 1 }}</td>
                                        <td class="text-left">{{ p.nombre }}</td>
                                        <td>{{ p.cantidad_vendida }}</td>
                                        <td>Bs. {{ formatNumber(p.total_ingresos) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="reporteData.alertas_stock?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Alertas de Stock</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>Producto</th><th>Stock</th><th>Stock Mínimo</th></tr></thead>
                                <tbody>
                                    <tr v-for="a in reporteData.alertas_stock" :key="a.id" class="text-center">
                                        <td>{{ a.nombre }}</td>
                                        <td class="text-danger font-weight-bold">{{ a.stock }}</td>
                                        <td>{{ a.stock_minimo }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="reporteData.metodos_pago?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-credit-card mr-1"></i> Métodos de Pago</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>Método</th><th>Cantidad</th><th>Monto Total</th></tr></thead>
                                <tbody>
                                    <tr v-for="m in reporteData.metodos_pago" :key="m.metodo_pago" class="text-center">
                                        <td>{{ capitalize(m.metodo_pago) }}</td>
                                        <td>{{ m.cantidad }}</td>
                                        <td>Bs. {{ formatNumber(m.monto_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="!reporteData.ventas_ultima_semana?.length && !reporteData.productos_mas_vendidos?.length && !reporteData.alertas_stock?.length && !reporteData.metodos_pago?.length" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i><p class="lead">Sin datos para el período</p>
                    </div>
                </template>

                <!-- Ventas -->
                <template v-if="filters.tipo_reporte === 'ventas' && reporteData">
                    <div class="row mb-3">
                        <div class="col-md-4"><div class="small-box bg-info p-3 rounded text-center"><div class="h5 mb-0">Bs. {{ formatNumber(reporteData.total_ingresos) }}</div><small>Total Ingresos</small></div></div>
                        <div class="col-md-4"><div class="small-box bg-success p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.total_ventas }}</div><small>Total Ventas</small></div></div>
                    </div>
                    <div v-if="reporteData.ventas_por_dia?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-chart-line mr-1"></i> Ventas por Día</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>Fecha</th><th>Cantidad</th><th>Total</th></tr></thead>
                                <tbody>
                                    <tr v-for="v in reporteData.ventas_por_dia" :key="v.fecha" class="text-center">
                                        <td>{{ formatDate(v.fecha) }}</td>
                                        <td>{{ v.cantidad }}</td>
                                        <td>Bs. {{ formatNumber(v.total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="reporteData.productos_mas_vendidos?.length">
                        <h5 class="mb-3"><i class="fas fa-fire mr-1"></i> Productos Más Vendidos</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>#</th><th>Producto</th><th>Cantidad</th><th>Total</th></tr></thead>
                                <tbody>
                                    <tr v-for="(p, i) in reporteData.productos_mas_vendidos" :key="p.id" class="text-center">
                                        <td>{{ i + 1 }}</td><td class="text-left">{{ p.nombre }}</td>
                                        <td>{{ p.cantidad_vendida }}</td>
                                        <td>Bs. {{ formatNumber(p.total_ingresos) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <!-- Pagos -->
                <template v-if="filters.tipo_reporte === 'pagos' && reporteData">
                    <div class="row mb-3">
                        <div class="col-md-4"><div class="small-box bg-info p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.total_pagos }}</div><small>Total Pagos</small></div></div>
                        <div class="col-md-4"><div class="small-box bg-success p-3 rounded text-center"><div class="h5 mb-0">Bs. {{ formatNumber(reporteData.monto_total) }}</div><small>Monto Total</small></div></div>
                    </div>
                    <div v-if="reporteData.pagos_por_dia?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-calendar-day mr-1"></i> Pagos por Día</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>Fecha</th><th>Cantidad</th><th>Monto</th></tr></thead>
                                <tbody>
                                    <tr v-for="p in reporteData.pagos_por_dia" :key="p.fecha" class="text-center">
                                        <td>{{ formatDate(p.fecha) }}</td>
                                        <td>{{ p.cantidad }}</td>
                                        <td>Bs. {{ formatNumber(p.monto_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if="reporteData.metodos_pago?.length">
                        <h5 class="mb-3"><i class="fas fa-credit-card mr-1"></i> Métodos de Pago</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark"><tr class="text-center"><th>Método</th><th>Cantidad</th><th>Monto</th></tr></thead>
                                <tbody>
                                    <tr v-for="m in reporteData.metodos_pago" :key="m.metodo_pago" class="text-center">
                                        <td>{{ capitalize(m.metodo_pago) }}</td>
                                        <td>{{ m.cantidad }}</td>
                                        <td>Bs. {{ formatNumber(m.monto_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <!-- Productos -->
                <template v-if="filters.tipo_reporte === 'productos' && reporteData">
                    <div class="row mb-3">
                        <div class="col-md-4"><div class="small-box bg-info p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.total_productos }}</div><small>Total Productos</small></div></div>
                        <div class="col-md-4"><div class="small-box bg-success p-3 rounded text-center"><div class="h5 mb-0">Bs. {{ formatNumber(reporteData.valor_inventario) }}</div><small>Valor Inventario</small></div></div>
                    </div>
                    <div v-if="reporteData.productos_mas_vendidos?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-fire mr-1"></i> Productos Más Vendidos</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>#</th><th>Producto</th><th>Cantidad</th><th>Total</th></tr></thead>
                            <tbody>
                                <tr v-for="(p, i) in reporteData.productos_mas_vendidos" :key="p.id" class="text-center">
                                    <td>{{ i + 1 }}</td><td class="text-left">{{ p.nombre }}</td>
                                    <td>{{ p.cantidad_vendida }}</td><td>Bs. {{ formatNumber(p.total_ingresos) }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                    <div v-if="reporteData.productos_por_categoria?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-tags mr-1"></i> Productos por Categoría</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>Categoría</th><th>Cantidad</th></tr></thead>
                            <tbody>
                                <tr v-for="c in reporteData.productos_por_categoria" :key="c.categoria" class="text-center">
                                    <td>{{ c.categoria }}</td><td>{{ c.cantidad }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                    <div v-if="reporteData.alertas_stock?.length">
                        <h5 class="mb-3"><i class="fas fa-exclamation-triangle text-danger mr-1"></i> Alertas de Stock</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>Producto</th><th>Stock</th><th>Stock Mínimo</th></tr></thead>
                            <tbody>
                                <tr v-for="a in reporteData.alertas_stock" :key="a.id" class="text-center">
                                    <td>{{ a.nombre }}</td><td class="text-danger font-weight-bold">{{ a.stock }}</td><td>{{ a.stock_minimo }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                </template>

                <!-- Inventario -->
                <template v-if="filters.tipo_reporte === 'inventario' && reporteData">
                    <div class="row mb-3">
                        <div class="col-md-4"><div class="small-box bg-info p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.total_productos }}</div><small>Total</small></div></div>
                        <div class="col-md-4"><div class="small-box bg-success p-3 rounded text-center"><div class="h5 mb-0">Bs. {{ formatNumber(reporteData.valor_total_inventario) }}</div><small>Valor Total</small></div></div>
                    </div>
                    <div v-if="reporteData.productos_por_categoria?.length" class="mb-4">
                        <h5 class="mb-3">Productos por Categoría</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>Categoría</th><th>Cantidad</th><th>Valor</th></tr></thead>
                            <tbody>
                                <tr v-for="c in reporteData.productos_por_categoria" :key="c.categoria" class="text-center">
                                    <td>{{ c.categoria }}</td><td>{{ c.cantidad }}</td><td>Bs. {{ formatNumber(c.valor_total) }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                    <div v-if="reporteData.alertas_stock?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-exclamation-triangle text-danger mr-1"></i> Alertas de Stock</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>Producto</th><th>Stock</th><th>Stock Mínimo</th></tr></thead>
                            <tbody>
                                <tr v-for="a in reporteData.alertas_stock" :key="a.id" class="text-center">
                                    <td>{{ a.nombre }}</td><td class="text-danger font-weight-bold">{{ a.stock }}</td><td>{{ a.stock_minimo }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                    <div v-if="reporteData.productos_sin_stock?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-times-circle text-danger mr-1"></i> Productos Sin Stock</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>#</th><th>Producto</th></tr></thead>
                            <tbody>
                                <tr v-for="(p, i) in reporteData.productos_sin_stock" :key="p.id" class="text-center">
                                    <td>{{ i + 1 }}</td><td>{{ p.nombre }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                    <div v-if="reporteData.productos_stock_bajo?.length">
                        <h5 class="mb-3"><i class="fas fa-exclamation-circle text-warning mr-1"></i> Productos con Stock Bajo (&gt;0 y &lt;10)</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>#</th><th>Producto</th><th>Stock</th></tr></thead>
                            <tbody>
                                <tr v-for="(p, i) in reporteData.productos_stock_bajo" :key="p.id" class="text-center">
                                    <td>{{ i + 1 }}</td><td>{{ p.nombre }}</td><td>{{ p.stock }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                </template>

                <!-- Clientes -->
                <template v-if="filters.tipo_reporte === 'clientes' && reporteData">
                    <div class="row mb-3">
                        <div class="col-md-3"><div class="small-box bg-info p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.total_clientes }}</div><small>Total</small></div></div>
                        <div class="col-md-3"><div class="small-box bg-success p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.clientes_activos }}</div><small>Activos</small></div></div>
                        <div class="col-md-3"><div class="small-box bg-warning p-3 rounded text-center"><div class="h5 mb-0">{{ reporteData.clientes_nuevos }}</div><small>Nuevos</small></div></div>
                    </div>
                    <div v-if="reporteData.mejores_clientes?.length" class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-trophy mr-1 text-warning"></i> Mejores Clientes</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>#</th><th>Cliente</th><th>Ventas</th><th>Total Gastado</th></tr></thead>
                            <tbody>
                                <tr v-for="(c, i) in reporteData.mejores_clientes" :key="c.id" class="text-center">
                                    <td>{{ i + 1 }}</td>
                                    <td class="text-left">{{ c.nombres }} {{ c.apellido_paterno || '' }}</td>
                                    <td>{{ c.ventas_count }}</td>
                                    <td>Bs. {{ formatNumber(c.ventas_sum_suma_total) }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                    <div v-if="reporteData.clientes_por_ciudad?.length">
                        <h5 class="mb-3"><i class="fas fa-map-marker-alt mr-1"></i> Clientes por Ciudad / Sexo</h5>
                        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark"><tr class="text-center"><th>Ciudad / Sexo</th><th>Cantidad</th></tr></thead>
                            <tbody>
                                <tr v-for="c in reporteData.clientes_por_ciudad" :key="c.ciudad" class="text-center">
                                    <td>{{ capitalize(c.ciudad) }}</td><td>{{ c.cantidad }}</td>
                                </tr>
                            </tbody>
                        </table></div>
                    </div>
                </template>

                <div v-if="!reporteData" class="text-center text-muted py-5">
                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                    <p class="lead">Seleccione un tipo de reporte, rango de fechas y genere</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import reporteService from '@/services/reportes';

const stats = ref({});
const filters = reactive({ tipo_reporte: 'dashboard', fecha_inicio: '', fecha_fin: '' });
const reporteData = ref(null);
const loading = ref(false);
const loadingPdf = ref(false);
const success = ref('');
const error = ref('');

const statBoxes = computed(() => [
    { label: 'Total Ventas', value: stats.value.total_ventas || 0, color: 'bg-success', icon: 'fas fa-shopping-cart' },
    { label: 'Ingresos del Mes', value: 'Bs. ' + formatNumber(stats.value.ingresos_mes || 0), color: 'bg-info', icon: 'fas fa-dollar-sign' },
    { label: 'Producto Más Vendido', value: stats.value.top_producto || '-', color: 'bg-warning', icon: 'fas fa-fire' },
    { label: 'Stock Bajo', value: stats.value.stock_bajo || 0, color: 'bg-danger', icon: 'fas fa-exclamation-triangle' },
]);

function formatNumber(num) {
    return Number(num || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function capitalize(str) {
    if (!str) return '-';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

async function loadStats() {
    try {
        const res = await reporteService.index();
        if (res.data?.stats) stats.value = res.data.stats;
    } catch (e) {
        console.error('Error loading stats:', e);
    }
}

async function generarReporte() {
    loading.value = true;
    error.value = '';
    success.value = '';
    reporteData.value = null;
    try {
        const params = { tipo_reporte: filters.tipo_reporte };
        if (filters.fecha_inicio) params.fecha_inicio = filters.fecha_inicio;
        if (filters.fecha_fin) params.fecha_fin = filters.fecha_fin;
        const res = await reporteService.datos(params);
        reporteData.value = res.data;
        success.value = 'Reporte generado exitosamente';
    } catch (e) {
        error.value = 'Error al generar el reporte';
    } finally {
        loading.value = false;
    }
}

async function descargarPdf() {
    loadingPdf.value = true;
    try {
        const params = { tipo_reporte: filters.tipo_reporte };
        if (filters.fecha_inicio) params.fecha_inicio = filters.fecha_inicio;
        if (filters.fecha_fin) params.fecha_fin = filters.fecha_fin;
        const res = await reporteService.descargarPdf(params);
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `reporte_${filters.tipo_reporte}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        success.value = 'PDF descargado exitosamente';
    } catch (e) {
        error.value = 'Error al descargar el PDF';
    } finally {
        loadingPdf.value = false;
    }
}

onMounted(() => loadStats());
</script>

<style scoped>
.form-control { border-radius: 8px; }

.small-box {
    color: #fff;
    min-height: 80px;
}

.small-box .h5 { font-weight: 700; }
.small-box small { opacity: 0.85; }

.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    background: #fff;
    display: flex;
    margin-bottom: 0;
    min-height: 80px;
    padding: 0.5rem;
    position: relative;
    transition: transform 0.2s;
}
.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.info-box .info-box-icon {
    border-radius: 0.25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
    color: #fff;
}
.info-box .info-box-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.8;
    flex: 1;
    padding: 0 10px;
}
.info-box .info-box-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 0.875rem;
}
.info-box .info-box-number {
    display: block;
    margin-top: 0.25rem;
    font-weight: bold;
    font-size: 1.5rem;
}
</style>
