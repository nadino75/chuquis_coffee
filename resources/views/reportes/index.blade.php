@extends('adminlte::page')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Reportes del Sistema</h3>
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
                                                    <option value="ventas" {{ $tipoReporte == 'ventas' ? 'selected' : '' }}>Ventas</option>
                                                    <option value="pagos" {{ $tipoReporte == 'pagos' ? 'selected' : '' }}>Pagos</option>
                                                    <option value="productos" {{ $tipoReporte == 'productos' ? 'selected' : '' }}>Productos</option>
                                                    <option value="inventario" {{ $tipoReporte == 'inventario' ? 'selected' : '' }}>Inventario</option>
                                                    <option value="clientes" {{ $tipoReporte == 'clientes' ? 'selected' : '' }}>Clientes</option>
                                                    <option value="general" {{ $tipoReporte == 'general' ? 'selected' : '' }}>General</option>
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
                                                <button type="button" id="descargarReporte" class="btn btn-success">
                                                    <i class="fas fa-download"></i> Descargar
                                                </button>
                                                <button type="button" id="guardarReporte" class="btn btn-info ml-2">
                                                    <i class="fas fa-save"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="row mb-4" id="estadisticasRapidas">
                        @include('reportes.partials.estadisticas-rapidas')
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-12">
                            <div id="contenedorGraficos">
                                @include('reportes.partials.graficos')
                            </div>
                        </div>
                    </div>

                    <!-- Tablas de Datos -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div id="contenedorTablas">
                                @include('reportes.partials.tablas-datos')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para guardar reporte -->
<div class="modal fade" id="modalGuardarReporte" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Guardar Reporte</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formGuardarReporte">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre_reporte">Nombre del Reporte</label>
                        <input type="text" class="form-control" id="nombre_reporte" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion_reporte">Descripción</label>
                        <textarea class="form-control" id="descripcion_reporte" name="descripcion" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="tipo" id="tipo_reporte_guardar">
                    <input type="hidden" name="fecha_inicio" id="fecha_inicio_guardar">
                    <input type="hidden" name="fecha_fin" id="fecha_fin_guardar">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
<style>
    .card-chart {
        height: 300px;
        position: relative;
    }
    .estadistica-card {
        border-left: 4px solid #007bff;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .estadistica-card.success { border-left-color: #28a745; }
    .estadistica-card.warning { border-left-color: #ffc107; }
    .estadistica-card.danger { border-left-color: #dc3545; }
    .estadistica-card.info { border-left-color: #17a2b8; }
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
    let charts = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Configurar filtros
        configurarFiltros();
        // Inicializar gráficos
        inicializarGraficos();
    });

    function configurarFiltros() {
        const formFiltro = document.getElementById('filtroReporte');
        const btnDescargar = document.getElementById('descargarReporte');
        const btnGuardar = document.getElementById('guardarReporte');

        formFiltro.addEventListener('submit', function(e) {
            e.preventDefault();
            actualizarReporte();
        });

        btnDescargar.addEventListener('click', function() {
            descargarReporte();
        });

        btnGuardar.addEventListener('click', function() {
            abrirModalGuardar();
        });

        // Configurar el formulario de guardar
        const formGuardar = document.getElementById('formGuardarReporte');
        formGuardar.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarReporte();
        });
    }

    function actualizarReporte() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);

        mostrarLoading();

        // Usar fetch para obtener solo los datos necesarios
        fetch(`{{ route('reportes.datos') }}?${params}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(datos => {
                // Actualizar estadísticas rápidas
                actualizarEstadisticasRapidas(datos, formData.get('tipo_reporte'));
                
                // Actualizar gráficos
                actualizarGraficos(datos, formData.get('tipo_reporte'));
                
                // Actualizar tablas
                actualizarTablas(datos, formData.get('tipo_reporte'));
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al actualizar el reporte: ' + error.message);
            })
            .finally(() => {
                ocultarLoading();
            });
    }

    function actualizarEstadisticasRapidas(datos, tipoReporte) {
        const container = document.getElementById('estadisticasRapidas');
        let html = '';

        if (tipoReporte === 'ventas' || tipoReporte === 'general') {
            html += `
                <div class="col-md-3">
                    <div class="estadistica-card">
                        <h5 class="text-primary">${datos.ventas_totales ? datos.ventas_totales.toLocaleString() : 0}</h5>
                        <p class="mb-0">Total Ventas</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="estadistica-card success">
                        <h5 class="text-success">$${datos.monto_total ? datos.monto_total.toLocaleString('es-ES', {minimumFractionDigits: 2}) : '0.00'}</h5>
                        <p class="mb-0">Monto Total</p>
                    </div>
                </div>
            `;
        }
        
        if (tipoReporte === 'pagos' || tipoReporte === 'general') {
            html += `
                <div class="col-md-3">
                    <div class="estadistica-card info">
                        <h5 class="text-info">${datos.pagos_totales ? datos.pagos_totales.toLocaleString() : 0}</h5>
                        <p class="mb-0">Total Pagos</p>
                    </div>
                </div>
            `;
        }
        
        if (tipoReporte === 'clientes' || tipoReporte === 'general') {
            html += `
                <div class="col-md-3">
                    <div class="estadistica-card warning">
                        <h5 class="text-warning">${datos.total_clientes ? datos.total_clientes.toLocaleString() : 0}</h5>
                        <p class="mb-0">Total Clientes</p>
                    </div>
                </div>
            `;
        }

        container.innerHTML = `<div class="row">${html}</div>`;
    }

    function actualizarGraficos(datos, tipoReporte) {
        // Destruir gráficos anteriores
        Object.values(charts).forEach(chart => {
            if (chart) chart.destroy();
        });
        charts = {};

        const contenedor = document.getElementById('contenedorGraficos');
        let html = '';

        if (tipoReporte === 'ventas' && datos.ventas_por_dia && datos.ventas_por_dia.length > 0) {
            html += `
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Ventas por Día</h6>
                        </div>
                        <div class="card-body">
                            <div class="card-chart">
                                <canvas id="chartVentas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (tipoReporte === 'pagos' && datos.pagos_por_metodo && datos.pagos_por_metodo.length > 0) {
            html += `
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Pagos por Método</h6>
                        </div>
                        <div class="card-body">
                            <div class="card-chart">
                                <canvas id="chartPagos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (tipoReporte === 'productos' && datos.productos_por_categoria && datos.productos_por_categoria.length > 0) {
            html += `
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Productos por Categoría</h6>
                        </div>
                        <div class="card-body">
                            <div class="card-chart">
                                <canvas id="chartCategorias"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        contenedor.innerHTML = `<div class="row">${html}</div>`;

        // Inicializar gráficos después de actualizar el HTML
        setTimeout(() => inicializarGraficosConDatos(datos, tipoReporte), 100);
    }

    function inicializarGraficosConDatos(datos, tipoReporte) {
        if (tipoReporte === 'ventas' && datos.ventas_por_dia) {
            const ctxVentas = document.getElementById('chartVentas');
            if (ctxVentas) {
                charts.ventas = new Chart(ctxVentas, {
                    type: 'line',
                    data: {
                        labels: datos.ventas_por_dia.map(d => d.fecha),
                        datasets: [{
                            label: 'Ventas por Día',
                            data: datos.ventas_por_dia.map(d => d.cantidad_ventas || d.total || 0),
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        if (tipoReporte === 'pagos' && datos.pagos_por_metodo) {
            const ctxPagos = document.getElementById('chartPagos');
            if (ctxPagos) {
                charts.pagos = new Chart(ctxPagos, {
                    type: 'doughnut',
                    data: {
                        labels: datos.pagos_por_metodo.map(d => d.metodo_pago || 'Sin método'),
                        datasets: [{
                            data: datos.pagos_por_metodo.map(d => d.cantidad || d.total || 0),
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

        if (tipoReporte === 'productos' && datos.productos_por_categoria) {
            const ctxCategorias = document.getElementById('chartCategorias');
            if (ctxCategorias) {
                charts.categorias = new Chart(ctxCategorias, {
                    type: 'bar',
                    data: {
                        labels: datos.productos_por_categoria.map(d => d.categoria || 'Sin categoría'),
                        datasets: [{
                            label: 'Cantidad de Productos',
                            data: datos.productos_por_categoria.map(d => d.cantidad || d.total || 0),
                            backgroundColor: '#28a745',
                            borderColor: '#1e7e34',
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

    function actualizarTablas(datos, tipoReporte) {
        const contenedor = document.getElementById('contenedorTablas');
        let html = '';

        // Aquí puedes agregar la lógica para actualizar las tablas según el tipo de reporte
        // Por ahora, mostramos un mensaje básico
        html += `
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Datos del Reporte - ${tipoReporte}</h6>
                    </div>
                    <div class="card-body">
                        <p>Reporte generado exitosamente. Total de registros procesados: ${Object.keys(datos).length} categorías de datos.</p>
                    </div>
                </div>
            </div>
        `;

        contenedor.innerHTML = `<div class="row">${html}</div>`;
    }

    function descargarReporte() {
        const formData = new FormData(document.getElementById('filtroReporte'));
        const params = new URLSearchParams(formData);

        window.open(`{{ route('reportes.descargar') }}?${params}`, '_blank');
    }

    function abrirModalGuardar() {
        const tipo = document.getElementById('tipo_reporte').value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        document.getElementById('tipo_reporte_guardar').value = tipo;
        document.getElementById('fecha_inicio_guardar').value = fechaInicio;
        document.getElementById('fecha_fin_guardar').value = fechaFin;

        $('#modalGuardarReporte').modal('show');
    }

    function guardarReporte() {
        const formData = new FormData(document.getElementById('formGuardarReporte'));

        fetch('{{ route("reportes.guardar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#modalGuardarReporte').modal('hide');
                mostrarExito('Reporte guardado correctamente');
                document.getElementById('formGuardarReporte').reset();
            } else {
                throw new Error(data.message || 'Error al guardar el reporte');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error al guardar el reporte: ' + error.message);
        });
    }

    function inicializarGraficos() {
        // Inicializar gráficos con datos iniciales
        const datosIniciales = @json($datosReporte);
        const tipoReporte = '{{ $tipoReporte }}';
        inicializarGraficosConDatos(datosIniciales, tipoReporte);
    }

    function mostrarLoading() {
        let overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        `;
        overlay.id = 'loadingOverlay';
        document.getElementById('contenedorGraficos').appendChild(overlay);
    }

    function ocultarLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    }

    function mostrarExito(mensaje) {
        // Usar Toast de Bootstrap o alert simple
        alert('Éxito: ' + mensaje);
    }

    function mostrarError(mensaje) {
        // Usar Toast de Bootstrap o alert simple
        alert('Error: ' + mensaje);
    }
</script>
@endpush