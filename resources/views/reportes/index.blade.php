@extends('adminlte::page')

@section('title', 'Reportes del Sistema')

@section('content_header')
    <h1><i class="fas fa-chart-bar"></i> Dashboard de Reportes</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Dashboard de Reportes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-info btn-sm" onclick="verificarDatos()">
                            <i class="fas fa-bug"></i> Debug
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="fas fa-filter"></i> Filtros del Reporte</h6>
                                </div>
                                <div class="card-body">
                                    <form id="filtroReporte" class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tipo_reporte">Tipo de Reporte</label>
                                                <select class="form-control" id="tipo_reporte" name="tipo_reporte">
                                                    <option value="dashboard" {{ $tipoReporte == 'dashboard' ? 'selected' : '' }}>Dashboard General</option>
                                                    <option value="ventas" {{ $tipoReporte == 'ventas' ? 'selected' : '' }}>Reporte de Ventas</option>
                                                    <option value="pagos" {{ $tipoReporte == 'pagos' ? 'selected' : '' }}>Reporte de Pagos</option>
                                                    <option value="productos" {{ $tipoReporte == 'productos' ? 'selected' : '' }}>Reporte de Productos</option>
                                                    <option value="inventario" {{ $tipoReporte == 'inventario' ? 'selected' : '' }}>Reporte de Inventario</option>
                                                    <option value="clientes" {{ $tipoReporte == 'clientes' ? 'selected' : '' }}>Reporte de Clientes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fecha_inicio">Fecha Inicio</label>
                                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fecha_fin">Fecha Fin</label>
                                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary mr-2">
                                                    <i class="fas fa-sync"></i> Actualizar
                                                </button>
                                                <button type="button" id="descargarPDF" class="btn btn-success">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Debug -->
                    <div id="debugInfo" class="alert alert-info" style="display: none;">
                        <h6><i class="fas fa-info-circle"></i> Información de Debug</h6>
                        <div id="debugContent"></div>
                    </div>

                    <!-- Estadísticas Básicas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ventas</span>
                                    <span class="info-box-number" id="totalVentas">
                                        {{ $datosReporte['estadisticas_generales']['total_ventas'] ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ingresos</span>
                                    <span class="info-box-number" id="totalIngresos">
                                        ${{ number_format($datosReporte['estadisticas_generales']['total_ingresos'] ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Clientes</span>
                                    <span class="info-box-number" id="totalClientes">
                                        {{ $datosReporte['estadisticas_generales']['total_clientes'] ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Productos</span>
                                    <span class="info-box-number" id="totalProductos">
                                        {{ $datosReporte['estadisticas_generales']['total_productos'] ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos Dinámicos -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Gráficos del Sistema</h6>
                                </div>
                                <div class="card-body">
                                    <div id="graficosDinamicos">
                                        @php
                                            $tieneDatosGraficos = 
                                                (isset($datosReporte['ventas_ultima_semana']) && $datosReporte['ventas_ultima_semana']->count() > 0) ||
                                                (isset($datosReporte['metodos_pago']) && $datosReporte['metodos_pago']->count() > 0) ||
                                                (isset($datosReporte['productos_mas_vendidos']) && $datosReporte['productos_mas_vendidos']->count() > 0);
                                        @endphp

                                        @if($tieneDatosGraficos)
                                            <div class="row">
                                                @if(isset($datosReporte['ventas_ultima_semana']) && $datosReporte['ventas_ultima_semana']->count() > 0)
                                                <div class="col-md-6 mb-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0"><i class="fas fa-chart-line"></i> Ventas por Día</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div style="height: 300px;">
                                                                <canvas id="chartVentasDia"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(isset($datosReporte['metodos_pago']) && $datosReporte['metodos_pago']->count() > 0)
                                                <div class="col-md-6 mb-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0"><i class="fas fa-chart-pie"></i> Métodos de Pago</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div style="height: 300px;">
                                                                <canvas id="chartMetodosPago"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(isset($datosReporte['productos_mas_vendidos']) && $datosReporte['productos_mas_vendidos']->count() > 0)
                                                <div class="col-md-6 mb-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0"><i class="fas fa-star"></i> Productos Más Vendidos</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div style="height: 300px;">
                                                                <canvas id="chartProductosVendidos"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(isset($datosReporte['ventas_por_vendedor']) && $datosReporte['ventas_por_vendedor']->count() > 0)
                                                <div class="col-md-6 mb-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0"><i class="fas fa-user-tie"></i> Ventas por Vendedor</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div style="height: 300px;">
                                                                <canvas id="chartVentasVendedor"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning text-center">
                                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                                <h5>No hay datos suficientes para mostrar gráficos</h5>
                                                <p class="mb-0">Usa los filtros y haz clic en "Actualizar" para generar reportes con datos.</p>
                                                <small>Los gráficos se mostrarán automáticamente cuando haya datos disponibles.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alertas de Stock -->
                    @if(isset($datosReporte['alertas_stock']) && $datosReporte['alertas_stock']->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="card-title mb-0"><i class="fas fa-exclamation-triangle"></i> Alertas de Stock</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Stock Actual</th>
                                                    <th>Stock Mínimo</th>
                                                    <th>Estado</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($datosReporte['alertas_stock'] as $alerta)
                                                <tr class="{{ $alerta->stock == 0 ? 'table-danger' : 'table-warning' }}">
                                                    <td>{{ $alerta->nombre }}</td>
                                                    <td>{{ $alerta->stock }}</td>
                                                    <td>{{ $alerta->stock_minimo ?? 10 }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $alerta->stock == 0 ? 'danger' : 'warning' }}">
                                                            {{ $alerta->stock == 0 ? 'SIN STOCK' : 'STOCK BAJO' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if(isset($alerta->id))
                                                        <a href="{{ route('productos.edit', $alerta->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i> Reabastecer
                                                        </a>
                                                        @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                                            <i class="fas fa-edit"></i> Reabastecer
                                                        </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> No hay alertas de stock en este momento. Todos los productos tienen stock suficiente.
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Datos en Crudo para Debug -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="fas fa-database"></i> Datos del Reporte (Debug)</h6>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" onclick="toggleDatosCrudos()">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" id="datosCrudosContainer" style="display: none;">
                                    <pre id="datosCrudos" style="max-height: 300px; overflow-y: auto; font-size: 12px; background: #f8f9fa; padding: 15px; border-radius: 4px;">
{{ json_encode($datosReporte, JSON_PRETTY_PRINT) }}
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: 0.25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: 0.5rem;
        position: relative;
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
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    // Variable global para almacenar los charts
    window.charts = {};

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Datos del reporte cargados:', @json($datosReporte));
        
        // Inicializar gráficos si hay datos
        inicializarGraficosIniciales();
        
        // Configurar event listeners
        const formFiltro = document.getElementById('filtroReporte');
        const btnDescargarPDF = document.getElementById('descargarPDF');

        formFiltro.addEventListener('submit', function(e) {
            e.preventDefault();
            actualizarReporte();
        });

        btnDescargarPDF.addEventListener('click', function() {
            descargarPDF();
        });
    });

    function inicializarGraficosIniciales() {
        const datos = @json($datosReporte);
        
        // Gráfico de ventas por día
        if (datos.ventas_ultima_semana && datos.ventas_ultima_semana.length > 0) {
            const ctx = document.getElementById('chartVentasDia');
            if (ctx) {
                const labels = datos.ventas_ultima_semana.map(d => {
                    const fecha = new Date(d.fecha);
                    return fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
                });
                const valores = datos.ventas_ultima_semana.map(d => d.total || d.cantidad);

                window.charts.ventasDia = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas ($)',
                            data: valores,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Monto ($)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Fecha'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            }
        }

        // Gráfico de métodos de pago
        if (datos.metodos_pago && datos.metodos_pago.length > 0) {
            const ctx = document.getElementById('chartMetodosPago');
            if (ctx) {
                const labels = datos.metodos_pago.map(d => {
                    const metodo = d.metodo_pago || 'No especificado';
                    return metodo.charAt(0).toUpperCase() + metodo.slice(1);
                });
                const valores = datos.metodos_pago.map(d => d.cantidad);

                window.charts.metodosPago = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: valores,
                            backgroundColor: [
                                '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                                '#e83e8c', '#fd7e14', '#20c997', '#6610f2', '#6c757d'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Gráfico de productos más vendidos
        if (datos.productos_mas_vendidos && datos.productos_mas_vendidos.length > 0) {
            const ctx = document.getElementById('chartProductosVendidos');
            if (ctx) {
                // Tomar solo los primeros 8 productos para mejor visualización
                const productos = datos.productos_mas_vendidos.slice(0, 8);
                const labels = productos.map(d => {
                    // Acortar nombres largos
                    const nombre = d.nombre || 'Producto';
                    return nombre.length > 20 ? nombre.substring(0, 20) + '...' : nombre;
                });
                const valores = productos.map(d => d.cantidad_vendida || 0);

                window.charts.productosVendidos = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Cantidad Vendida',
                            data: valores,
                            backgroundColor: '#28a745',
                            borderColor: '#1e7e34',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Cantidad Vendida'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Productos'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarReporte() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);

        // Mostrar loading
        mostrarLoading();

        fetch(`{{ route('reportes.datos') }}?${params}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(datos => {
                console.log('Datos actualizados:', datos);
                
                // Actualizar estadísticas
                actualizarEstadisticas(datos);
                
                // Actualizar gráficos
                actualizarGraficosDinamicos(datos);
                
                // Actualizar datos en crudo
                document.getElementById('datosCrudos').textContent = JSON.stringify(datos, null, 2);
                
                mostrarExito('Reporte actualizado correctamente');
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al actualizar el reporte: ' + error.message);
            })
            .finally(() => {
                ocultarLoading();
            });
    }

    function actualizarEstadisticas(datos) {
        document.getElementById('totalVentas').textContent = datos.estadisticas_generales?.total_ventas || 0;
        document.getElementById('totalIngresos').textContent = '$' + (datos.estadisticas_generales?.total_ingresos || 0).toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('totalClientes').textContent = datos.estadisticas_generales?.total_clientes || 0;
        document.getElementById('totalProductos').textContent = datos.estadisticas_generales?.total_productos || 0;
    }

    function actualizarGraficosDinamicos(datos) {
        const contenedor = document.getElementById('graficosDinamicos');
        
        // Destruir gráficos anteriores
        Object.values(window.charts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        window.charts = {};

        // Verificar si hay datos para gráficos
        const tieneDatosGraficos = 
            (datos.ventas_ultima_semana && datos.ventas_ultima_semana.length > 0) ||
            (datos.metodos_pago && datos.metodos_pago.length > 0) ||
            (datos.productos_mas_vendidos && datos.productos_mas_vendidos.length > 0);

        if (!tieneDatosGraficos) {
            contenedor.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <h5>No hay datos suficientes para gráficos</h5>
                    <p class="mb-0">No se encontraron datos con los filtros actuales.</p>
                    <small>Intenta con un rango de fechas diferente o verifica que hayan registros en el sistema.</small>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';

        // Gráfico de ventas por día
        if (datos.ventas_ultima_semana && datos.ventas_ultima_semana.length > 0) {
            html += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fas fa-chart-line"></i> Ventas por Día</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="chartVentasDia"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Gráfico de métodos de pago
        if (datos.metodos_pago && datos.metodos_pago.length > 0) {
            html += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fas fa-chart-pie"></i> Métodos de Pago</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="chartMetodosPago"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Gráfico de productos más vendidos
        if (datos.productos_mas_vendidos && datos.productos_mas_vendidos.length > 0) {
            html += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fas fa-star"></i> Productos Más Vendidos</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="chartProductosVendidos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        html += '</div>';
        contenedor.innerHTML = html;

        // Re-inicializar gráficos después de actualizar el DOM
        setTimeout(() => inicializarGraficosIniciales(), 100);
    }

    function descargarPDF() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);
        window.open(`{{ route('reportes.descargar-pdf') }}?${params}`, '_blank');
    }

    function verificarDatos() {
        const debugContent = `
            <p><strong>Ventas en BD:</strong> {{ \App\Models\Venta::count() }}</p>
            <p><strong>Pagos en BD:</strong> {{ \App\Models\Pago::count() }}</p>
            <p><strong>Productos en BD:</strong> {{ \App\Models\Producto::count() }}</p>
            <p><strong>Clientes en BD:</strong> {{ \App\Models\Cliente::count() }}</p>
            <p><strong>Fecha actual:</strong> ${new Date().toLocaleString('es-ES')}</p>
        `;
        document.getElementById('debugContent').innerHTML = debugContent;
        document.getElementById('debugInfo').style.display = 'block';
    }

    function toggleDatosCrudos() {
        const container = document.getElementById('datosCrudosContainer');
        if (container.style.display === 'none') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function mostrarLoading() {
        let overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>';
        overlay.id = 'loadingOverlay';
        document.getElementById('graficosDinamicos').appendChild(overlay);
    }

    function ocultarLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.remove();
    }

    function mostrarExito(mensaje) {
        // Usar toastr si está disponible, sino usar alert nativo
        if (typeof toastr !== 'undefined') {
            toastr.success(mensaje);
        } else {
            alert('Éxito: ' + mensaje);
        }
    }

    function mostrarError(mensaje) {
        if (typeof toastr !== 'undefined') {
            toastr.error(mensaje);
        } else {
            alert('Error: ' + mensaje);
        }
    }
</script>
@endpush