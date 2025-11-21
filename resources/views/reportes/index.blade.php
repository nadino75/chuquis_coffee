@extends('adminlte::page')

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
                                    <span class="info-box-text">Total Pagos</span>
                                    <span class="info-box-number" id="totalPagos">
                                        {{ $datosReporte['estadisticas_generales']['total_pagos'] ?? 0 }}
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

                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Gráficos del Sistema</h6>
                                </div>
                                <div class="card-body">
                                    <div id="contenedorGraficos">
                                        @include('reportes.partials.graficos-simples')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos en Crudo para Debug -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="fas fa-database"></i> Datos del Reporte (Debug)</h6>
                                </div>
                                <div class="card-body">
                                    <pre id="datosCrudos" style="max-height: 300px; overflow-y: auto; font-size: 12px;">
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
@endsection

@push('styles')
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Datos del reporte:', @json($datosReporte));
        
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

    function actualizarReporte() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);

        fetch(`{{ route('reportes.datos') }}?${params}`)
            .then(response => response.json())
            .then(datos => {
                console.log('Datos actualizados:', datos);
                
                // Actualizar estadísticas
                document.getElementById('totalVentas').textContent = datos.estadisticas_generales?.total_ventas || 0;
                document.getElementById('totalPagos').textContent = datos.estadisticas_generales?.total_pagos || 0;
                document.getElementById('totalClientes').textContent = datos.estadisticas_generales?.total_clientes || 0;
                document.getElementById('totalProductos').textContent = datos.estadisticas_generales?.total_productos || 0;
                
                // Actualizar datos en crudo
                document.getElementById('datosCrudos').textContent = JSON.stringify(datos, null, 2);
                
                mostrarExito('Reporte actualizado correctamente');
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al actualizar el reporte: ' + error.message);
            });
    }

    function descargarPDF() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);
        window.open(`{{ route('reportes.descargar-pdf') }}?${params}`, '_blank');
    }

    function verificarDatos() {
        fetch('{{ route("reportes.debug") }}')
@extends('adminlte::page')

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
                                    <span class="info-box-text">Total Pagos</span>
                                    <span class="info-box-number" id="totalPagos">
                                        {{ $datosReporte['estadisticas_generales']['total_pagos'] ?? 0 }}
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

                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Gráficos del Sistema</h6>
                                </div>
                                <div class="card-body">
                                    <div id="graficosDinamicos">
                                        @if(isset($datosReporte['ventas_ultima_semana']) && $datosReporte['ventas_ultima_semana']->count() > 0)
                                            <div class="row">
                                                @if($datosReporte['ventas_ultima_semana']->count() > 0)
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
                                            <div class="alert alert-info text-center">
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
@endsection

@push('styles')
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
</style>
@endpush

@push('scripts')
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
                const values = datos.ventas_ultima_semana.map(d => d.cantidad);

                window.charts.ventasDia = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas',
                            data: values,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
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
                const labels = datos.metodos_pago.map(d => d.metodo_pago || 'No especificado');
                const values = datos.metodos_pago.map(d => d.cantidad);

                window.charts.metodosPago = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: [
                                '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                                '#e83e8c', '#fd7e14', '#20c997', '#6610f2', '#6c757d'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
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
                const labels = datos.productos_mas_vendidos.map(d => d.nombre).slice(0, 8);
                const values = datos.productos_mas_vendidos.map(d => d.cantidad_vendida).slice(0, 8);

                window.charts.productosVendidos = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Cantidad Vendida',
                            data: values,
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
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        // Gráfico de ventas por vendedor
        if (datos.ventas_por_vendedor && datos.ventas_por_vendedor.length > 0) {
            const ctx = document.getElementById('chartVentasVendedor');
            if (ctx) {
                const labels = datos.ventas_por_vendedor.map(d => d.vendedor).slice(0, 6);
                const values = datos.ventas_por_vendedor.map(d => d.cantidad).slice(0, 6);

                window.charts.ventasVendedor = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas',
                            data: values,
                            backgroundColor: '#ffc107',
                            borderColor: '#e0a800',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
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
                    throw new Error('Error en la respuesta del servidor');
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
                
                // Actualizar alertas de stock
                actualizarAlertasStock(datos);
                
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
        document.getElementById('totalPagos').textContent = datos.estadisticas_generales?.total_pagos || 0;
        document.getElementById('totalClientes').textContent = datos.estadisticas_generales?.total_clientes || 0;
        document.getElementById('totalProductos').textContent = datos.estadisticas_generales?.total_productos || 0;
    }

    function actualizarGraficosDinamicos(datos) {
        const contenedor = document.getElementById('graficosDinamicos');
        
        // Destruir gráficos anteriores
        Object.values(window.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
        window.charts = {};

        // Verificar si hay datos para gráficos
        const tieneDatosGraficos = 
            (datos.ventas_ultima_semana && datos.ventas_ultima_semana.length > 0) ||
            (datos.metodos_pago && datos.metodos_pago.length > 0) ||
            (datos.productos_mas_vendidos && datos.productos_mas_vendidos.length > 0) ||
            (datos.ventas_por_vendedor && datos.ventas_por_vendedor.length > 0);

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
                            <div style="height: 300px;">
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
                            <div style="height: 300px;">
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
                            <div style="height: 300px;">
                                <canvas id="chartProductosVendidos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Gráfico de ventas por vendedor
        if (datos.ventas_por_vendedor && datos.ventas_por_vendedor.length > 0) {
            html += `
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
            `;
        }

        html += '</div>';
        contenedor.innerHTML = html;

        // Re-inicializar gráficos después de actualizar el DOM
        setTimeout(() => inicializarGraficosIniciales(), 100);
    }

    function actualizarAlertasStock(datos) {
        // Esta función puede expandirse para actualizar dinámicamente las alertas de stock
        console.log('Alertas de stock actualizadas:', datos.alertas_stock);
    }

    function descargarPDF() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);
        window.open(`{{ route('reportes.descargar-pdf') }}?${params}`, '_blank');
    }

    function verificarDatos() {
        fetch('{{ route("reportes.debug") }}')
            .then(response => response.json())
            .then(datos => {
                const debugContent = `
                    <p><strong>Ventas en BD:</strong> ${datos.ventas_count}</p>
                    <p><strong>Pagos en BD:</strong> ${datos.pagos_count}</p>
                    <p><strong>Productos en BD:</strong> ${datos.productos_count}</p>
                    <p><strong>Clientes en BD:</strong> ${datos.clientes_count}</p>
                    <p><strong>Tablas existentes:</strong> ${datos.tablas_existentes.map(t => Object.values(t)[0]).join(', ')}</p>
                `;
                document.getElementById('debugContent').innerHTML = debugContent;
                document.getElementById('debugInfo').style.display = 'block';
            })
            .catch(error => {
                console.error('Error en debug:', error);
                mostrarError('Error en verificación de datos');
            });
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