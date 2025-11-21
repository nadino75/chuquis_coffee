@extends('adminlte::page')

@section('title', 'Dashboard - Sistema de Gestión')

@section('content_header')
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard del Sistema</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Estadísticas Principales -->
    <div class="row">
        @foreach($estadisticas as $key => $estadistica)
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-{{ $estadistica['color'] }}">
                    <i class="{{ $estadistica['icon'] }}"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $estadistica['titulo'] }}</span>
                    <span class="info-box-number">
                        @if(in_array($key, ['ventas_hoy', 'ventas_mes', 'ingresos_totales']))
                            ${{ number_format($estadistica['ingresos'] ?? $estadistica['total'], 2) }}
                        @else
                            {{ number_format($estadistica['total']) }}
                        @endif
                    </span>
                    @if(isset($estadistica['ingresos']) && $key != 'ingresos_totales')
                    <small>{{ number_format($estadistica['total']) }} ventas</small>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Ventas últimos 7 días -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Ventas - Últimos 7 Días</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="chartVentas7Dias" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-credit-card"></i> Métodos de Pago</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="chartMetodosPago" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Productos Más Vendidos -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star"></i> Productos Más Vendidos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="chartProductosVendidos" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas por Categoría -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tags"></i> Ventas por Categoría</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="chartVentasCategoria" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas y Ventas Recientes -->
    <div class="row">
        <!-- Alertas del Sistema -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bell"></i> Alertas del Sistema</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">{{ count($alertas) }} alertas</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($alertas) > 0)
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @foreach($alertas as $alerta)
                        <li class="item">
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    <i class="{{ $alerta['icon'] }} text-{{ $alerta['tipo'] }}"></i>
                                    {{ $alerta['mensaje'] }}
                                    <span class="badge badge-{{ $alerta['tipo'] }} float-right">{{ $alerta['fecha'] }}</span>
                                </a>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center p-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted">No hay alertas en este momento</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ventas Recientes -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Ventas Recientes</h3>
                    <div class="card-tools">
                        <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-list"></i> Ver Todas
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($ventasRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasRecientes as $venta)
                                <tr>
                                    <td>{{ $venta->producto->nombre ?? 'N/A' }}</td>
                                    <td>{{ $venta->cliente->nombre ?? 'N/A' }}</td>
                                    <td>${{ number_format($venta->total, 2) }}</td>
                                    <td>{{ $venta->created_at->format('d/m H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center p-4">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No hay ventas recientes</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
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
    .chart {
        position: relative;
        height: 300px;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para los gráficos
        const datosGraficos = @json($datosGraficos);

        // 1. Gráfico de Ventas últimos 7 días
        if (document.getElementById('chartVentas7Dias')) {
            const ctx = document.getElementById('chartVentas7Dias').getContext('2d');
            const ventas7Dias = datosGraficos.ventas_7_dias || [];
            
            const labels = ventas7Dias.map(v => {
                const fecha = new Date(v.fecha);
                return fecha.toLocaleDateString('es-ES', { weekday: 'short', day: '2-digit' });
            });
            const datos = ventas7Dias.map(v => v.total || 0);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: datos,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Ventas: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Gráfico de Métodos de Pago
        if (document.getElementById('chartMetodosPago')) {
            const ctx = document.getElementById('chartMetodosPago').getContext('2d');
            const metodosPago = datosGraficos.metodos_pago || [];
            
            const labels = metodosPago.map(m => {
                const metodo = m.tipo_pago || 'No especificado';
                return metodo.charAt(0).toUpperCase() + metodo.slice(1);
            });
            const datos = metodosPago.map(m => m.cantidad);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: datos,
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                            '#e83e8c', '#fd7e14', '#20c997'
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

        // 3. Gráfico de Productos Más Vendidos
        if (document.getElementById('chartProductosVendidos')) {
            const ctx = document.getElementById('chartProductosVendidos').getContext('2d');
            const productosVendidos = datosGraficos.productos_mas_vendidos || [];
            
            const labels = productosVendidos.map(p => {
                const nombre = p.nombre || 'Producto';
                return nombre.length > 20 ? nombre.substring(0, 20) + '...' : nombre;
            }).slice(0, 8);
            const datos = productosVendidos.map(p => p.cantidad_vendida).slice(0, 8);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: datos,
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
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // 4. Gráfico de Ventas por Categoría
        if (document.getElementById('chartVentasCategoria')) {
            const ctx = document.getElementById('chartVentasCategoria').getContext('2d');
            const ventasCategoria = datosGraficos.ventas_por_categoria || [];
            
            const labels = ventasCategoria.map(v => v.categoria || 'Sin categoría');
            const datos = ventasCategoria.map(v => v.total || 0);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: datos,
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Actualizar dashboard cada 2 minutos
        setInterval(actualizarDashboard, 120000);

        function actualizarDashboard() {
            fetch('{{ route("dashboard.datos") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Dashboard actualizado');
                        // Aquí podrías actualizar los datos en tiempo real
                    }
                })
                .catch(error => console.error('Error actualizando dashboard:', error));
        }
    });
</script>
@stop