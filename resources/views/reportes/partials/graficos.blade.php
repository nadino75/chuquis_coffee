<div class="row">
    @if($tipoReporte == 'ventas' && isset($datosReporte['ventas_por_dia']) && $datosReporte['ventas_por_dia']->count() > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-chart-line"></i> Ventas por Día</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartVentas" 
                            data-labels="{{ $datosReporte['ventas_por_dia']->pluck('fecha')->toJson() }}"
                            data-values="{{ $datosReporte['ventas_por_dia']->pluck('cantidad_ventas')->toJson() }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tipoReporte == 'pagos' && isset($datosReporte['pagos_por_metodo']) && $datosReporte['pagos_por_metodo']->count() > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-chart-pie"></i> Pagos por Método</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartPagos"
                            data-labels="{{ $datosReporte['pagos_por_metodo']->pluck('metodo_pago')->toJson() }}"
                            data-values="{{ $datosReporte['pagos_por_metodo']->pluck('cantidad')->toJson() }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tipoReporte == 'productos' && isset($datosReporte['productos_por_categoria']) && $datosReporte['productos_por_categoria']->count() > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Productos por Categoría</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartCategorias"
                            data-labels="{{ $datosReporte['productos_por_categoria']->pluck('categoria')->toJson() }}"
                            data-values="{{ $datosReporte['productos_por_categoria']->pluck('cantidad')->toJson() }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tipoReporte == 'clientes' && isset($datosReporte['clientes_por_ciudad']) && $datosReporte['clientes_por_ciudad']->count() > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-map-marker-alt"></i> Clientes por Ciudad</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartClientesCiudad"
                            data-labels="{{ $datosReporte['clientes_por_ciudad']->pluck('ciudad')->toJson() }}"
                            data-values="{{ $datosReporte['clientes_por_ciudad']->pluck('cantidad')->toJson() }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tipoReporte == 'general')
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> El reporte general muestra un resumen de todas las áreas del sistema.
        </div>
    </div>
    @endif

    @if(!isset($datosReporte) || (empty($datosReporte['ventas_por_dia']) && empty($datosReporte['pagos_por_metodo']) && empty($datosReporte['productos_por_categoria'])))
    <div class="col-md-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No hay datos suficientes para mostrar gráficos con los filtros actuales.
        </div>
    </div>
    @endif
</div>