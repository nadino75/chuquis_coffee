@extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
<h1><i class="fas fa-tags"></i> Marcas</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center bg-primary text-white">
        <h4 class="mb-0">Lista de Marcas</h4>
        <div class="ml-auto">
            <!-- Botón Crear Nuevo -->
            @can('crear-marca')
            <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createMarcaModal">
                <i class="fa fa-plus-circle"></i> Crear Nuevo
            </button>
            @endcan
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover text-center">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marcas as $marca)
                <tr>
                    <td>{{ $marca->id }}</td>
                    <td>{{ $marca->nombre }}</td>
                    <td>
                        <!-- Botón Ver -->
                        @can('ver-marca')
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#showMarcaModal{{ $marca->id }}">
                            <i class="fa fa-eye"></i> Ver
                        </button>
                        @endcan

                        <!-- Botón Editar -->
                        @can('editar-marca')
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#editMarcaModal{{ $marca->id }}">
                            <i class="fa fa-edit"></i> Editar
                        </button>

                        <!-- Botón Eliminar -->
                        @can('borrar-marca')
                        <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Desea eliminar esta marca?');">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                

                <!-- Modal Ver -->
                <div class="modal fade" id="showMarcaModal{{ $marca->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5>Detalles de Marca</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p><strong>ID:</strong> {{ $marca->id }}</p>
                                <p><strong>Nombre:</strong> {{ $marca->nombre }}</p>
                                <p><strong>Creado:</strong> {{ $marca->created_at }}</p>
                                <p><strong>Actualizado:</strong> {{ $marca->updated_at }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Editar -->
                <div class="modal fade" id="editMarcaModal{{ $marca->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5>Editar Marca</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('marcas.update', $marca->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" name="nombre" value="{{ old('nombre', $marca->nombre) }}" class="form-control">
                                        @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success">Actualizar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createMarcaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5>Crear Marca</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('marcas.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
