<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte del Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .info { margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section h2 { background-color: #f8f9fa; padding: 10px; border-left: 4px solid #007bff; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .total { font-weight: bold; background-color: #e9ecef; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte del Sistema</h1>
        <p><strong>Tipo:</strong> {{ ucfirst($tipoReporte) }} | 
           <strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p><strong>Generado:</strong> {{ $fechaGeneracion }}</p>
    </div>

    @if($tipoReporte == 'ventas' || $tipoReporte == 'general')
    <div class="section">
        <h2>Resumen de Ventas</h2>
        <div class="info">
            <p><strong>Total de Ventas:</strong> {{ number_format($datos['ventas_totales'] ?? 0) }}</p>
            <p><strong>Monto Total:</strong> ${{ number_format($datos['monto_total'] ?? 0, 2) }}</p>
        </div>

        @if(isset($datos['ventas_por_dia']) && count($datos['ventas_por_dia']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Ventas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['ventas_por_dia'] as $venta)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $venta->cantidad }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    @if($tipoReporte == 'pagos' || $tipoReporte == 'general')
    <div class="section">
        <h2>Resumen de Pagos</h2>
        <div class="info">
            <p><strong>Total de Pagos:</strong> {{ number_format($datos['pagos_totales'] ?? 0) }}</p>
            <p><strong>Monto Total:</strong> ${{ number_format($datos['monto_total'] ?? 0, 2) }}</p>
        </div>

        @if(isset($datos['pagos_por_metodo']) && count($datos['pagos_por_metodo']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Cantidad</th>
                    <th>Monto Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['pagos_por_metodo'] as $pago)
                <tr>
                    <td>{{ $pago->metodo_pago ?? 'No especificado' }}</td>
                    <td>{{ $pago->cantidad }}</td>
                    <td>${{ number_format($pago->monto_total ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    @if($tipoReporte == 'productos' || $tipoReporte == 'inventario' || $tipoReporte == 'general')
    <div class="section">
        <h2>Resumen de Productos</h2>
        <div class="info">
            <p><strong>Total de Productos:</strong> {{ number_format($datos['total_productos'] ?? 0) }}</p>
            <p><strong>Valor del Inventario:</strong> ${{ number_format($datos['valor_inventario'] ?? 0, 2) }}</p>
            <p><strong>Productos con Stock Bajo:</strong> {{ number_format($datos['productos_stock_bajo'] ?? 0) }}</p>
        </div>
    </div>
    @endif

    @if($tipoReporte == 'clientes' || $tipoReporte == 'general')
    <div class="section">
        <h2>Resumen de Clientes</h2>
        <div class="info">
            <p><strong>Total de Clientes:</strong> {{ number_format($datos['total_clientes'] ?? 0) }}</p>
            <p><strong>Clientes Activos:</strong> {{ number_format($datos['clientes_activos'] ?? 0) }}</p>
            <p><strong>Clientes Nuevos:</strong> {{ number_format($datos['clientes_nuevos'] ?? 0) }}</p>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión</p>
        <p>Página {{ $pdf->getPage() }} de {{ $pdf->getPageCount() }}</p>
    </div>
</body>
</html>