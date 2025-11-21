@extends('adminlte::page')

@section('title', 'Productos')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4 class="mb-0">Productos</h4>
            <div class="ml-auto">
                @can('crear-producto')
                <button class="btn btn-primary" data-toggle="modal" data-target="#createProductoModal">
                    <i class="fa fa-plus"></i> Crear Nuevo
                </button>
                @endcan
            </div>
        </div>


        <div class="card-body table-responsive">
            <table class="table table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Categoria</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $i => $producto)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->stock }}</td>
                            <td>{{ $producto->precio }}</td>
                            <td>{{ $producto->categoria->nombre }}</td>
                            <td>
                                @can('ver-producto')
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#showProductoModal{{ $producto->id }}"><i class="fa fa-eye"></i></button>
                                @endcan
                                @can('editar-producto')
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#editProductoModal{{ $producto->id }}"><i class="fa fa-edit"></i></button>
                                @endcan
                                @can('borrar-producto')
                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Eliminar producto?') ? this.closest('form').submit() : false;">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>

                        {{-- Incluir modales --}}
                        @include('producto.show', ['producto' => $producto])
                        @include('producto.edit', ['producto' => $producto])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal crear --}}
@include('producto.create')
@stop

@section('js')
<script>
$(document).ready(function(){
    $('.modal').on('hidden.bs.modal', function () {
        var form = $(this).find('form')[0];
        if(form) form.reset();
    });
});
</script>
@stop
