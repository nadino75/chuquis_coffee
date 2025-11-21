<div class="row">
    @if($tipoReporte == 'ventas' || $tipoReporte == 'general')
    <div class="col-md-3">
        <div class="estadistica-card">
            <h5 class="text-primary">{{ number_format($datosReporte['ventas_totales'] ?? 0) }}</h5>
            <p class="mb-0">Total Ventas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="estadistica-card success">
            <h5 class="text-success">${{ number_format($datosReporte['monto_total'] ?? 0, 2) }}</h5>
            <p class="mb-0">Monto Total</p>
        </div>
    </div>
    @endif
    
    @if($tipoReporte == 'pagos' || $tipoReporte == 'general')
    <div class="col-md-3">
        <div class="estadistica-card info">
            <h5 class="text-info">{{ number_format($datosReporte['pagos_totales'] ?? 0) }}</h5>
            <p class="mb-0">Total Pagos</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="estadistica-card warning">
            <h5 class="text-warning">${{ number_format($datosReporte['monto_total_pagos'] ?? $datosReporte['monto_total'] ?? 0, 2) }}</h5>
            <p class="mb-0">Monto Pagos</p>
        </div>
    </div>
    @endif
    
    @if($tipoReporte == 'clientes' || $tipoReporte == 'general')
    <div class="col-md-3">
        <div class="estadistica-card warning">
            <h5 class="text-warning">{{ number_format($datosReporte['total_clientes'] ?? 0) }}</h5>
            <p class="mb-0">Total Clientes</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="estadistica-card danger">
            <h5 class="text-danger">{{ number_format($datosReporte['clientes_activos'] ?? 0) }}</h5>
            <p class="mb-0">Clientes Activos</p>
        </div>
    </div>
    @endif

    @if($tipoReporte == 'productos' || $tipoReporte == 'inventario' || $tipoReporte == 'general')
    <div class="col-md-3">
        <div class="estadistica-card primary">
            <h5 class="text-primary">{{ number_format($datosReporte['total_productos'] ?? 0) }}</h5>
            <p class="mb-0">Total Productos</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="estadistica-card danger">
            <h5 class="text-danger">{{ number_format($datosReporte['productos_stock_bajo'] ?? 0) }}</h5>
            <p class="mb-0">Stock Bajo</p>
        </div>
    </div>
    @endif
</div>