<div class="row">
    @if($tipoReporte == 'ventas')
        <!-- Tabla de Ventas por Día -->
        @if(isset($datosReporte['ventas_por_dia']) && $datosReporte['ventas_por_dia']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-calendar-day"></i> Ventas por Día</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['ventas_por_dia'] as $venta)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                    <td class="text-right">{{ $venta->cantidad_ventas }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if(isset($datosReporte['ventas_totales']))
                            <tfoot>
                                <tr class="table-primary">
                                    <th>Total</th>
                                    <th class="text-right">{{ $datosReporte['ventas_totales'] }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Ventas por Vendedor -->
        @if(isset($datosReporte['ventas_por_vendedor']) && $datosReporte['ventas_por_vendedor']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-user-tie"></i> Ventas por Vendedor</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Vendedor</th>
                                    <th class="text-right">Ventas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['ventas_por_vendedor'] as $vendedor)
                                <tr>
                                    <td>{{ $vendedor->vendedor ?? 'N/A' }}</td>
                                    <td class="text-right">{{ $vendedor->cantidad_ventas }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Productos Más Vendidos -->
        @if(isset($datosReporte['productos_mas_vendidos']) && $datosReporte['productos_mas_vendidos']->count() > 0)
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-star"></i> Productos Más Vendidos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Cantidad Vendida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['productos_mas_vendidos'] as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-right">{{ $producto->cantidad_vendida }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @elseif($tipoReporte == 'pagos')
        <!-- Tabla de Pagos por Método -->
        @if(isset($datosReporte['pagos_por_metodo']) && $datosReporte['pagos_por_metodo']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-credit-card"></i> Pagos por Método</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Método de Pago</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['pagos_por_metodo'] as $pago)
                                <tr>
                                    <td>{{ $pago->metodo_pago ?? 'No especificado' }}</td>
                                    <td class="text-right">{{ $pago->cantidad }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if(isset($datosReporte['pagos_totales']))
                            <tfoot>
                                <tr class="table-primary">
                                    <th>Total</th>
                                    <th class="text-right">{{ $datosReporte['pagos_totales'] }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Pagos por Estado -->
        @if(isset($datosReporte['pagos_por_estado']) && $datosReporte['pagos_por_estado']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-info-circle"></i> Pagos por Estado</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['pagos_por_estado'] as $estado)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $estado->estado == 'completado' ? 'success' : ($estado->estado == 'pendiente' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($estado->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-right">{{ $estado->cantidad }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @elseif($tipoReporte == 'productos')
        <!-- Tabla de Productos por Categoría -->
        @if(isset($datosReporte['productos_por_categoria']) && $datosReporte['productos_por_categoria']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-tags"></i> Productos por Categoría</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['productos_por_categoria'] as $categoria)
                                <tr>
                                    <td>{{ $categoria->categoria ?? 'Sin categoría' }}</td>
                                    <td class="text-right">{{ $categoria->cantidad }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if(isset($datosReporte['total_productos']))
                            <tfoot>
                                <tr class="table-primary">
                                    <th>Total</th>
                                    <th class="text-right">{{ $datosReporte['total_productos'] }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Productos Vendidos -->
        @if(isset($datosReporte['productos_vendidos']) && $datosReporte['productos_vendidos']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-chart-line"></i> Productos Más Vendidos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Vendidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['productos_vendidos']->take(10) as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-right">{{ $producto->cantidad_vendida }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @elseif($tipoReporte == 'clientes')
        <!-- Tabla de Mejores Clientes -->
        @if(isset($datosReporte['mejores_clientes']) && $datosReporte['mejores_clientes']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-trophy"></i> Mejores Clientes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th class="text-right">Compras</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['mejores_clientes'] as $cliente)
                                <tr>
                                    <td>{{ $cliente->nombre ?? 'Cliente #' . $cliente->id }}</td>
                                    <td class="text-right">{{ $cliente->ventas_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Clientes por Ciudad -->
        @if(isset($datosReporte['clientes_por_ciudad']) && $datosReporte['clientes_por_ciudad']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-map-marker-alt"></i> Clientes por Ciudad</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Ciudad</th>
                                    <th class="text-right">Clientes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['clientes_por_ciudad'] as $ciudad)
                                <tr>
                                    <td>{{ $ciudad->ciudad ?? 'No especificada' }}</td>
                                    <td class="text-right">{{ $ciudad->cantidad }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if(isset($datosReporte['total_clientes']))
                            <tfoot>
                                <tr class="table-primary">
                                    <th>Total</th>
                                    <th class="text-right">{{ $datosReporte['total_clientes'] }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @elseif($tipoReporte == 'inventario')
        <!-- Tabla de Productos con Stock Bajo -->
        @if(isset($datosReporte['productos_stock_bajo']) && $datosReporte['productos_stock_bajo']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-exclamation-triangle text-warning"></i> Productos con Stock Bajo</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Stock Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['productos_stock_bajo'] as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-warning">{{ $producto->stock }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Productos Sin Stock -->
        @if(isset($datosReporte['productos_sin_stock']) && $datosReporte['productos_sin_stock']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-times-circle text-danger"></i> Productos Sin Stock</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosReporte['productos_sin_stock'] as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-danger">{{ $producto->stock }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @elseif($tipoReporte == 'general')
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> El reporte general muestra estadísticas consolidas de todas las áreas del sistema.
            </div>
            
            <!-- Resumen General -->
            <div class="row">
                @if(isset($datosReporte['resumen_ventas']))
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fas fa-shopping-cart"></i> Resumen Ventas</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Ventas:</strong> {{ $datosReporte['resumen_ventas']['ventas_totales'] ?? 0 }}</p>
                            <p><strong>Monto Total:</strong> ${{ number_format($datosReporte['resumen_ventas']['monto_total'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($datosReporte['resumen_pagos']))
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fas fa-money-bill-wave"></i> Resumen Pagos</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Pagos:</strong> {{ $datosReporte['resumen_pagos']['pagos_totales'] ?? 0 }}</p>
                            <p><strong>Monto Total:</strong> ${{ number_format($datosReporte['resumen_pagos']['monto_total'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    @endif

    @if(!isset($datosReporte) || (empty($datosReporte) && (!isset($datosReporte['ventas_por_dia']) && !isset($datosReporte['pagos_por_metodo']) && !isset($datosReporte['productos_por_categoria']))))
    <div class="col-md-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No hay datos disponibles para mostrar en las tablas con los filtros actuales.
        </div>
    </div>
    @endif
</div>