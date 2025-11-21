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
                                    <a href="{{ route('productos.edit', $alerta->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Reabastecer
                                    </a>
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