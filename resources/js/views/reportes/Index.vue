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
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Ventas</span>
                        <span class="info-box-number">{{ stats.total_ventas || 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ingresos del Mes</span>
                        <span class="info-box-number">${{ formatNumber(stats.ingresos_mes || 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-fire"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Productos Más Vendidos</span>
                        <span class="info-box-number">{{ stats.top_producto || '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stock Bajo</span>
                        <span class="info-box-number">{{ stats.stock_bajo || 0 }}</span>
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
                    <div class="col-md-3">
                        <label class="font-weight-bold">Fecha Inicio</label>
                        <input type="date" v-model="filters.start_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Fecha Fin</label>
                        <input type="date" v-model="filters.end_date" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary" @click="generarReporte" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                            <i class="fas fa-file-alt mr-1"></i> Generar Reporte
                        </button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-danger" @click="descargarPdf" :disabled="loadingPdf" v-if="reporteData?.length">
                            <span v-if="loadingPdf" class="spinner-border spinner-border-sm mr-1"></span>
                            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
                        </button>
                    </div>
                </div>

                <div class="table-responsive" v-if="reporteData?.length">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, idx) in reporteData" :key="idx" class="text-center">
                                <td>{{ idx + 1 }}</td>
                                <td>{{ formatDate(item.fecha || item.created_at) }}</td>
                                <td>{{ item.producto?.nombre || item.producto_nombre || item.nombre || '-' }}</td>
                                <td>{{ item.cantidad || 0 }}</td>
                                <td>${{ formatNumber(item.precio_unitario || item.precio || 0) }}</td>
                                <td>${{ formatNumber(item.total || item.suma_total || 0) }}</td>
                            </tr>
                        </tbody>
                        <tfoot v-if="totales">
                            <tr class="bg-light font-weight-bold">
                                <td colspan="3" class="text-right">TOTALES:</td>
                                <td>{{ totales.cantidad }}</td>
                                <td>-</td>
                                <td>${{ formatNumber(totales.total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div v-else-if="reporteGenerado" class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="lead">No se encontraron registros para el rango seleccionado</p>
                </div>

                <div v-else class="text-center text-muted py-5">
                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                    <p class="lead">Seleccione un rango de fechas y genere el reporte</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import reporteService from '@/services/reportes';

const stats = ref({});
const filters = reactive({ start_date: '', end_date: '' });
const reporteData = ref([]);
const loading = ref(false);
const loadingPdf = ref(false);
const success = ref('');
const error = ref('');
const reporteGenerado = ref(false);

const totales = reactive({ cantidad: 0, total: 0 });

function formatNumber(num) {
    return Number(num || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function calcularTotales() {
    let cantidad = 0;
    let total = 0;
    reporteData.value.forEach(item => {
        cantidad += Number(item.cantidad || 0);
        total += Number(item.total || item.suma_total || 0);
    });
    totales.cantidad = cantidad;
    totales.total = total;
}

async function loadStats() {
    try {
        const res = await reporteService.index();
        if (res.data) {
            stats.value = res.data.stats || res.data.estadisticas || {};
            reporteData.value = res.data.reportes || res.data.data || [];
            if (reporteData.value.length) {
                reporteGenerado.value = true;
                calcularTotales();
            }
        }
    } catch (e) {
        console.error('Error cargando estadísticas:', e);
    }
}

async function generarReporte() {
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
        const params = {};
        if (filters.start_date) params.start_date = filters.start_date;
        if (filters.end_date) params.end_date = filters.end_date;
        const res = await reporteService.datos(params);
        reporteData.value = res.data.reportes || res.data.data || [];
        reporteGenerado.value = true;
        calcularTotales();
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
        const params = {};
        if (filters.start_date) params.start_date = filters.start_date;
        if (filters.end_date) params.end_date = filters.end_date;
        const res = await reporteService.descargarPdf(params);
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'reporte.pdf');
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
.dBlock { display: block !important; }
.form-control { border-radius: 8px; }
.btn-group .btn { margin: 0 2px; }
.invalid-feedback { display: block; }

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
