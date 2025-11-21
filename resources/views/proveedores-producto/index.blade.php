@extends('adminlte::page')

@section('title', 'Proveedores Productos')

@section('content_header')
<h1><i class="fas fa-truck"></i> Proveedores Productos</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center bg-primary text-white">
        <h4 class="mb-0">Lista de Proveedores Productos</h4>
        <div class="ml-auto">
            <!-- Botón Crear Nuevo -->
            <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createProveedoresProductoModal">
                <i class="fa fa-plus-circle"></i> Crear Nuevo
            </button>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover text-center">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Proveedor</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Precio</th>
                    <th>Fecha Compra</th>
                    <th>Fecha Vencimiento</th>
                    <th>Marca</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proveedoresProductos as $item)
                <tr>
                    <td>{{ $loop->iteration + ($proveedoresProductos->currentPage() - 1) * $proveedoresProductos->perPage() }}</td>
                    <td>{{ $item->proveedore->nombre ?? '-' }}</td>
                    <td>{{ $item->producto->nombre ?? '-' }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ $item->unidad }}</td>
                    <td>${{ number_format($item->precio, 2) }}</td>
                    <td>{{ $item->fecha_compra }}</td>
                    <td>{{ $item->fecha_vencimiento ?? '-' }}</td>
                    <td>{{ $item->marca->nombre ?? '-' }}</td>
                    <td>
                        <!-- Botón Ver -->
                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                data-target="#showProveedoresProductoModal{{ $item->id }}">
                            <i class="fa fa-eye"></i> Ver
                        </button>

                        <!-- Botón Editar -->
                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#editProveedoresProductoModal{{ $item->id }}">
                            <i class="fa fa-edit"></i> Editar
                        </button>

                        <!-- Botón Eliminar -->
                        <form action="{{ route('proveedores_productos.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Desea eliminar este registro?');">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $proveedoresProductos->links() }}
    </div>
</div>

<!-- Modal Create -->
@include('proveedores-producto.create', [
    'proveedores' => $proveedores,
    'productos' => $productos,
    'marcas' => $marcas,
    'unidades' => ['unidad','kg','litro','bolsa']
])

<!-- Modales Show y Edit - FUERA de la tabla -->
@foreach($proveedoresProductos as $item)
    <!-- Modal Show -->
    @include('proveedores-producto.show', ['proveedoresProducto' => $item])

    <!-- Modal Edit -->
    @include('proveedores-producto.edit', [
        'proveedoresProducto' => $item,
        'proveedores' => $proveedores,
        'productos' => $productos,
        'marcas' => $marcas,
        'unidades' => ['unidad','kg','litro','bolsa']
    ])
@endforeach

@stop

@section('js')
<script>
$(document).ready(function(){
    // Resetea el formulario al cerrar el modal
    $('.modal').on('hidden.bs.modal', function () {
        var form = $(this).find('form')[0];
        if(form) form.reset();
    });
});
</script>
@stop