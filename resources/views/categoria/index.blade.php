@extends('adminlte::page')

@section('title', 'Categorías')

@section('content_header')
<h1><i class="fas fa-folder"></i> Categorías</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center bg-primary text-white">
        <h4 class="mb-0">Lista de Categorías</h4>
        <div class="ml-auto">
            <!-- Botón Crear Nuevo -->
            <button type="button" class="btn btn-light btn-sm" 
                    data-toggle="modal" data-target="#createCategoriaModal">
                <i class="fa fa-plus-circle"></i> Crear Nuevo
            </button>
        </div>
    </div>


    <div class="card-body">
        <table class="table table-bordered table-hover text-center">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo de producto</th>
                    <th>Categoria Padre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->id }}</td>
                    <td>{{ $categoria->nombre }}</td>
                    <td>{{ $categoria->descripcion }}</td>
                    <td>{{ $categoria->tipo->nombre ?? '-' }}</td>
                    <td>{{ $categoria->categoria_padre->nombre ?? '-' }}</td>
                    <td>
                        <!-- Botón Ver -->
                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                data-target="#showCategoriaModal{{ $categoria->id }}">
                            <i class="fa fa-eye"></i> Ver
                        </button>

                        <!-- Botón Editar -->
                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#editCategoriaModal{{ $categoria->id }}">
                            <i class="fa fa-edit"></i> Editar
                        </button>

                        <!-- Botón Eliminar -->
                        <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Desea eliminar esta categoría?');">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Ver -->
                @include('categoria.show', ['categoria' => $categoria])

                <!-- Modal Editar -->
                @include('categoria.edit', ['categoria' => $categoria, 'tipos' => $tipos, 'categoriasPadre' => $categoriasPadre])

                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
@include('categoria.create', ['tipos' => $tipos, 'categoriasPadre' => $categoriasPadre])

@stop

@section('js')
<script>
    // Resetear formularios al cerrar modales
    $(document).ready(function(){
        $('.modal').on('hidden.bs.modal', function () {
            var form = $(this).find('form')[0];
            if(form) form.reset();
        });
    });
</script>
@stop
