@extends('adminlte::page')

@section('title', 'Detalle de Venta')

@section('content_header')
    <h1 class="d-flex align-items-center">
        <i class="fas fa-eye fa-lg mr-2"></i>
        Detalle de Venta
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-receipt mr-1"></i> Recibo: {{ $venta->pago->recibo ?? 'N/A' }}
            </h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-shopping-bag mr-1"></i> Información de la Venta
                    </h5>
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="40%">Cliente:</th>
                            <td>
                                {{ $venta->cliente->nombres ?? 'N/A' }} 
                                {{ $venta->cliente->apellido_paterno ?? '' }}
                                <br>
                                <small class="text-muted">CI: {{ $venta->cliente_ci }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Producto:</th>
                            <td>{{ $venta->producto->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Cantidad:</th>
                            <td>{{ $venta->cantidad }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Precio Unitario:</th>
                            <td>${{ number_format($venta->precio, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Subtotal:</th>
                            <td>${{ number_format($venta->precio * $venta->cantidad, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">IVA (13%):</th>
                            <td>${{ number_format(($venta->precio * $venta->cantidad) * 0.13, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Total Venta:</th>
                            <td class="font-weight-bold text-success">
                                ${{ number_format(($venta->precio * $venta->cantidad) * 1.13, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-credit-card mr-1"></i> Información del Pago
                    </h5>
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="40%">Fecha y Hora:</th>
                            <td>{{ $venta->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tipo de Pago:</th>
                            <td>
                                <span class="badge badge-warning">
                                    {{ ucfirst($venta->pago->tipo_pago ?? 'N/A') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Total Pagado:</th>
                            <td class="font-weight-bold text-primary">
                                ${{ number_format($venta->pago->total_pagado ?? 0, 2) }}
                            </td>
                        </tr>
                    </table>

                    <!-- Información del Stock -->
                    <h6 class="mt-4">
                        <i class="fas fa-boxes mr-1"></i> Estado del Stock
                    </h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th class="bg-light">Producto:</th>
                            <td>{{ $venta->producto->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Stock Actual:</th>
                            <td>
                                <span class="badge {{ $venta->producto->stock > 10 ? 'badge-success' : ($venta->producto->stock > 0 ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $venta->producto->stock ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
                </a>
                <a href="{{ route('ventas.create') }}" class="btn btn-success ml-2">
                    <i class="fas fa-plus mr-1"></i> Nueva Venta
                </a>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th {
            font-weight: 600;
        }
        .badge {
            font-size: 0.75rem;
        }
    </style>
@stop