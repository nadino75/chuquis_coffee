<div class="row mb-4">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Ventas</span>
                <span class="info-box-number">{{ number_format($datosReporte['estadisticas_generales']['total_ventas'] ?? 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Pagos</span>
                <span class="info-box-number">{{ number_format($datosReporte['estadisticas_generales']['total_pagos'] ?? 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Clientes</span>
                <span class="info-box-number">{{ number_format($datosReporte['estadisticas_generales']['total_clientes'] ?? 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Productos</span>
                <span class="info-box-number">{{ number_format($datosReporte['estadisticas_generales']['total_productos'] ?? 0) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos del Dashboard -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-chart-line"></i> Ventas de la Última Semana</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartVentasSemana"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-chart-pie"></i> Métodos de Pago</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartMetodosPago"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-star"></i> Productos Más Vendidos</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartProductosVendidos"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-user-tie"></i> Ventas por Vendedor</h6>
            </div>
            <div class="card-body">
                <div class="card-chart">
                    <canvas id="chartVentasVendedor"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas de Stock -->
@include('reportes.partials.alertas-stock')