@extends('adminlte::page')

@section('title', 'Registro de Ventas')

@section('content_header')
    <h1 class="d-flex align-items-center">
        <i class="fas fa-cash-register fa-lg mr-2"></i>
        Registro de Ventas
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-success d-flex align-items-center">
            <h5 class="card-title mb-0 text-white flex-grow-1">
                <i class="fas fa-list mr-1"></i> Historial de Ventas
            </h5>
            @can('crear-venta')
            <a href="{{ route('ventas.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle mr-1"></i> Nueva Venta
            </a>
            @endcan
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th width="12%">Recibo</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th width="8%">Cantidad</th>
                            <th width="10%">Precio Unit.</th>
                            <th width="10%">Total</th>
                            <th width="12%">Tipo Pago</th>
                            <th width="12%">Fecha</th>
                            <th width="15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($ventas->currentPage() - 1) * $ventas->perPage() }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $venta->pago->recibo ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    {{ $venta->cliente->nombres ?? 'N/A' }} 
                                    {{ $venta->cliente->apellido_paterno ?? '' }}
                                </td>
                                <td>{{ $venta->producto->nombre ?? 'N/A' }}</td>
                                <td class="text-center">{{ $venta->cantidad }}</td>
                                <td class="text-right">${{ number_format($venta->precio, 2) }}</td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($venta->precio * $venta->cantidad, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($venta->pago)
                                        <span class="badge badge-warning">{{ ucfirst($venta->pago->tipo_pago) }}</span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        @can('borrar-venta')
                                        <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta venta? Se restaurará el stock del producto.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-cash-register fa-2x mb-2"></i><br>
                                    No hay ventas registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ventas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} registros
                    </div>
                    {{ $ventas->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th { 
            font-weight: 600; 
            font-size: 0.85rem;
            vertical-align: middle;
        }
        .btn-group .btn { 
            margin: 0 2px; 
        }
        .badge {
            font-size: 0.75rem;
        }
    </style>
@stop