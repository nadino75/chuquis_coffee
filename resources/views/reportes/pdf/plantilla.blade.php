<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte del Sistema</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .info-section h2 {
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .alert-section {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .mb-20 {
            margin-bottom: 20px;
        }
        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte del Sistema</h1>
        <div class="subtitle">
            {{ ucfirst($tipoReporte) }} - 
            Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} 
            al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
        <div class="subtitle">
            Generado: {{ $fechaGeneracion }}
        </div>
    </div>

    @if(isset($datos['estadisticas_generales']))
    <div class="info-section">
        <h2>Estadísticas Generales</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $datos['estadisticas_generales']['total_ventas'] ?? 0 }}</div>
                <div class="stat-label">Total Ventas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${{ number_format($datos['estadisticas_generales']['total_ingresos'] ?? 0, 2) }}</div>
                <div class="stat-label">Total Ingresos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $datos['estadisticas_generales']['total_pagos'] ?? 0 }}</div>
                <div class="stat-label">Total Pagos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $datos['estadisticas_generales']['total_clientes'] ?? 0 }}</div>
                <div class="stat-label">Total Clientes</div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($datos['ventas_ultima_semana']) && count($datos['ventas_ultima_semana']) > 0)
    <div class="info-section">
        <h2>Ventas por Día</h2>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad de Ventas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['ventas_ultima_semana'] as $venta)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $venta->cantidad }}</td>
                    <td class="text-right">${{ number_format($venta->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($datos['productos_mas_vendidos']) && count($datos['productos_mas_vendidos']) > 0)
    <div class="info-section">
        <h2>Productos Más Vendidos</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Vendida</th>
                    <th>Total Ingresos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['productos_mas_vendidos'] as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td class="text-center">{{ $producto->cantidad_vendida }}</td>
                    <td class="text-right">${{ number_format($producto->total_ingresos, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($datos['metodos_pago']) && count($datos['metodos_pago']) > 0)
    <div class="info-section">
        <h2>Métodos de Pago</h2>
        <table>
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Cantidad</th>
                    <th>Monto Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['metodos_pago'] as $metodo)
                <tr>
                    <td>{{ ucfirst($metodo->metodo_pago) }}</td>
                    <td class="text-center">{{ $metodo->cantidad }}</td>
                    <td class="text-right">${{ number_format($metodo->monto_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($datos['alertas_stock']) && count($datos['alertas_stock']) > 0)
    <div class="info-section">
        <h2>Alertas de Stock</h2>
        <div class="alert-section {{ count($datos['alertas_stock']) > 5 ? 'alert-danger' : 'alert-warning' }}">
            <strong>Productos con stock bajo:</strong> {{ count($datos['alertas_stock']) }}
        </div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['alertas_stock'] as $alerta)
                <tr>
                    <td>{{ $alerta->nombre }}</td>
                    <td class="text-center">{{ $alerta->stock }}</td>
                    <td class="text-center">{{ $alerta->stock_minimo ?? 10 }}</td>
                    <td class="text-center">
                        @if($alerta->stock == 0)
                            <strong>SIN STOCK</strong>
                        @else
                            STOCK BAJO
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($datos['error']))
    <div class="info-section alert-danger">
        <h2>Error en el Reporte</h2>
        <p><strong>Mensaje de error:</strong> {{ $datos['error'] }}</p>
    </div>
    @endif

    @if(!isset($datos['estadisticas_generales']) && !isset($datos['error']))
    <div class="info-section">
        <h2>Sin Datos</h2>
        <p>No se encontraron datos para el reporte solicitado con los filtros aplicados.</p>
    </div>
    @endif

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión Chuquis Coffee</p>
        <p>Página 1 de 1 - Generado el {{ $fechaGeneracion }}</p>
    </div>
</body>
</html>