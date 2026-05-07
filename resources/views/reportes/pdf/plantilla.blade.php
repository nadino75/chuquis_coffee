<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte {{ ucfirst($tipoReporte) }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        h1 { text-align: center; font-size: 16pt; margin-bottom: 5px; }
        h2 { font-size: 13pt; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-top: 20px; }
        .subtitle { text-align: center; color: #666; font-size: 9pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #2c3e50; color: #fff; padding: 6px 8px; text-align: center; font-size: 9pt; }
        td { padding: 5px 8px; border: 1px solid #ddd; font-size: 9pt; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background: #e8e8e8 !important; }
        .stats { display: flex; justify-content: space-around; margin: 15px 0; }
        .stat-box { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 4px; min-width: 120px; }
        .stat-box .label { font-size: 8pt; color: #666; text-transform: uppercase; }
        .stat-box .value { font-size: 14pt; font-weight: bold; color: #2c3e50; }
        .footer { text-align: center; color: #999; font-size: 8pt; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <h1>Reporte de {{ ucfirst($tipoReporte) }}</h1>
    <p class="subtitle">
        Período: {{ $fechaInicio }} al {{ $fechaFin }} &mdash; Generado: {{ $fechaGeneracion }}
    </p>

    @php $d = $datos; @endphp

    @if($tipoReporte === 'dashboard' || $tipoReporte === 'ventas')
        @if(!empty($d['estadisticas_generales']) || !empty($d['total_ventas']))
            <div class="stats">
                @if(!empty($d['estadisticas_generales']))
                    <div class="stat-box"><div class="label">Ventas</div><div class="value">{{ $d['estadisticas_generales']['total_ventas'] ?? 0 }}</div></div>
                    <div class="stat-box"><div class="label">Ingresos</div><div class="value">Bs. {{ number_format($d['estadisticas_generales']['total_ingresos'] ?? 0, 2) }}</div></div>
                    <div class="stat-box"><div class="label">Clientes</div><div class="value">{{ $d['estadisticas_generales']['total_clientes'] ?? 0 }}</div></div>
                    <div class="stat-box"><div class="label">Productos</div><div class="value">{{ $d['estadisticas_generales']['total_productos'] ?? 0 }}</div></div>
                @else
                    <div class="stat-box"><div class="label">Total Ventas</div><div class="value">{{ $d['total_ventas'] ?? 0 }}</div></div>
                    <div class="stat-box"><div class="label">Total Ingresos</div><div class="value">Bs. {{ number_format($d['total_ingresos'] ?? 0, 2) }}</div></div>
                @endif
            </div>
        @endif

        @if(!empty($d['ventas_ultima_semana']) || !empty($d['ventas_por_dia']))
            @php $ventasDia = $d['ventas_ultima_semana'] ?? $d['ventas_por_dia'] ?? []; @endphp
            <h2>Ventas por Día</h2>
            <table>
                <thead><tr><th>Fecha</th><th>Cantidad</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($ventasDia as $v)
                        <tr>
                            <td class="text-center">{{ $v->fecha ?? $v['fecha'] ?? '-' }}</td>
                            <td class="text-center">{{ $v->cantidad ?? $v['cantidad'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($v->total ?? $v['total'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        @if(!empty($d['productos_mas_vendidos']))
            <h2>Productos Más Vendidos</h2>
            <table>
                <thead><tr><th>#</th><th>Producto</th><th>Cantidad Vendida</th><th>Total Ingresos</th></tr></thead>
                <tbody>
                    @foreach($d['productos_mas_vendidos'] as $i => $p)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $p->nombre ?? $p['nombre'] ?? '-' }}</td>
                            <td class="text-center">{{ $p->cantidad_vendida ?? $p['cantidad_vendida'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($p->total_ingresos ?? $p['total_ingresos'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    @if($tipoReporte === 'pagos')
        <div class="stats">
            <div class="stat-box"><div class="label">Total Pagos</div><div class="value">{{ $d['total_pagos'] ?? 0 }}</div></div>
            <div class="stat-box"><div class="label">Monto Total</div><div class="value">Bs. {{ number_format($d['monto_total'] ?? 0, 2) }}</div></div>
        </div>

        @if(!empty($d['pagos_por_dia']))
            <h2>Pagos por Día</h2>
            <table>
                <thead><tr><th>Fecha</th><th>Cantidad</th><th>Monto Total</th></tr></thead>
                <tbody>
                    @forelse($d['pagos_por_dia'] as $p)
                        <tr>
                            <td class="text-center">{{ $p->fecha ?? $p['fecha'] ?? '-' }}</td>
                            <td class="text-center">{{ $p->cantidad ?? $p['cantidad'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($p->monto_total ?? $p['monto_total'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        @if(!empty($d['metodos_pago']))
            <h2>Métodos de Pago</h2>
            <table>
                <thead><tr><th>Método</th><th>Cantidad</th><th>Monto Total</th></tr></thead>
                <tbody>
                    @forelse($d['metodos_pago'] as $m)
                        <tr>
                            <td>{{ ucfirst($m->metodo_pago ?? $m['metodo_pago'] ?? '-') }}</td>
                            <td class="text-center">{{ $m->cantidad ?? $m['cantidad'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($m->monto_total ?? $m['monto_total'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @endif

    @if($tipoReporte === 'productos')
        <div class="stats">
            <div class="stat-box"><div class="label">Total Productos</div><div class="value">{{ $d['total_productos'] ?? 0 }}</div></div>
            <div class="stat-box"><div class="label">Valor Inventario</div><div class="value">Bs. {{ number_format($d['valor_inventario'] ?? 0, 2) }}</div></div>
        </div>

        @if(!empty($d['productos_mas_vendidos']))
            <h2>Productos Más Vendidos</h2>
            <table>
                <thead><tr><th>#</th><th>Producto</th><th>Cantidad</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($d['productos_mas_vendidos'] as $i => $p)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $p->nombre ?? $p['nombre'] ?? '-' }}</td>
                            <td class="text-center">{{ $p->cantidad_vendida ?? $p['cantidad_vendida'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($p->total_ingresos ?? $p['total_ingresos'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(!empty($d['productos_por_categoria']))
            <h2>Productos por Categoría</h2>
            <table>
                <thead><tr><th>Categoría</th><th>Cantidad</th></tr></thead>
                <tbody>
                    @forelse($d['productos_por_categoria'] as $c)
                        <tr>
                            <td>{{ $c->categoria ?? $c['categoria'] ?? '-' }}</td>
                            <td class="text-center">{{ $c->cantidad ?? $c['cantidad'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        @if(!empty($d['alertas_stock']))
            <h2>Alertas de Stock</h2>
            <table>
                <thead><tr><th>Producto</th><th>Stock Actual</th><th>Stock Mínimo</th></tr></thead>
                <tbody>
                    @forelse($d['alertas_stock'] as $a)
                        <tr>
                            <td>{{ $a->nombre ?? $a['nombre'] ?? '-' }}</td>
                            <td class="text-center">{{ $a->stock ?? $a['stock'] ?? 0 }}</td>
                            <td class="text-center">{{ $a->stock_minimo ?? $a['stock_minimo'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin alertas</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @endif

    @if($tipoReporte === 'inventario')
        <div class="stats">
            <div class="stat-box"><div class="label">Total Productos</div><div class="value">{{ $d['total_productos'] ?? 0 }}</div></div>
            <div class="stat-box"><div class="label">Valor Total</div><div class="value">Bs. {{ number_format($d['valor_total_inventario'] ?? 0, 2) }}</div></div>
        </div>

        @if(!empty($d['productos_por_categoria']))
            <h2>Productos por Categoría</h2>
            <table>
                <thead><tr><th>Categoría</th><th>Cantidad</th><th>Valor Total</th></tr></thead>
                <tbody>
                    @forelse($d['productos_por_categoria'] as $c)
                        <tr>
                            <td>{{ $c->categoria ?? $c['categoria'] ?? '-' }}</td>
                            <td class="text-center">{{ $c->cantidad ?? $c['cantidad'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($c->valor_total ?? $c['valor_total'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        @if(!empty($d['alertas_stock']))
            <h2>Alertas de Stock (stock &lt; 10)</h2>
            <table>
                <thead><tr><th>Producto</th><th>Stock</th><th>Stock Mínimo</th></tr></thead>
                <tbody>
                    @forelse($d['alertas_stock'] as $a)
                        <tr>
                            <td>{{ $a->nombre ?? $a['nombre'] ?? '-' }}</td>
                            <td class="text-center">{{ $a->stock ?? $a['stock'] ?? 0 }}</td>
                            <td class="text-center">{{ $a->stock_minimo ?? $a['stock_minimo'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin alertas</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        @if(!empty($d['productos_sin_stock']))
            <h2>Productos Sin Stock</h2>
            <table>
                <thead><tr><th>#</th><th>Producto</th></tr></thead>
                <tbody>
                    @forelse($d['productos_sin_stock'] as $i => $p)
                        <tr><td class="text-center">{{ $i + 1 }}</td><td>{{ $p->nombre ?? $p['nombre'] ?? '-' }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-center">Ninguno</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @endif

    @if($tipoReporte === 'clientes')
        <div class="stats">
            <div class="stat-box"><div class="label">Total Clientes</div><div class="value">{{ $d['total_clientes'] ?? 0 }}</div></div>
            <div class="stat-box"><div class="label">Clientes Activos</div><div class="value">{{ $d['clientes_activos'] ?? 0 }}</div></div>
            <div class="stat-box"><div class="label">Clientes Nuevos</div><div class="value">{{ $d['clientes_nuevos'] ?? 0 }}</div></div>
        </div>

        @if(!empty($d['mejores_clientes']))
            <h2>Mejores Clientes</h2>
            <table>
                <thead><tr><th>#</th><th>Cliente</th><th>Ventas</th><th>Total Gastado</th></tr></thead>
                <tbody>
                    @foreach($d['mejores_clientes'] as $i => $c)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $c->nombres ?? $c['nombres'] ?? '-' }} {{ $c->apellido_paterno ?? $c['apellido_paterno'] ?? '' }}</td>
                            <td class="text-center">{{ $c->ventas_count ?? $c['ventas_count'] ?? 0 }}</td>
                            <td class="text-right">Bs. {{ number_format($c->ventas_sum_suma_total ?? $c['ventas_sum_suma_total'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(!empty($d['clientes_por_ciudad']))
            <h2>Clientes por Ciudad / Sexo</h2>
            <table>
                <thead><tr><th>Ciudad / Sexo</th><th>Cantidad</th></tr></thead>
                <tbody>
                    @forelse($d['clientes_por_ciudad'] as $c)
                        <tr>
                            <td>{{ ucfirst($c->ciudad ?? $c['ciudad'] ?? '-') }}</td>
                            <td class="text-center">{{ $c->cantidad ?? $c['cantidad'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @endif

    @if(empty($d) || (empty($d['estadisticas_generales']) && empty($d['ventas_por_dia']) && empty($d['pagos_por_dia']) && empty($d['productos_mas_vendidos']) && empty($d['mejores_clientes'])))
        <p class="text-center" style="margin-top: 40px; color: #999;">Sin datos disponibles para el período seleccionado.</p>
    @endif

    <div class="footer">
        Chuquis Coffee &mdash; Reporte generado automáticamente el {{ $fechaGeneracion }}
    </div>
</body>
</html>
