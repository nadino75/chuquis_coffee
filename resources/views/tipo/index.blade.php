{{-- resources/views/tipo/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Tipos de Producto')

@section('content_header')
<h1><i class="fas fa-tags"></i> Tipos de Producto</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">Lista de Tipos</h4>
        <div class="ml-auto">
            <!-- Botón Crear Nuevo -->
            <button type="button" class="btn btn-light btn-sm" 
                    data-toggle="modal"
                    data-target="#createTipoModal">
                <i class="fa fa-plus-circle"></i> Crear Nuevo
            </button>
        </div>
    </div>


    <div class="card-body">
        <table class="table table-bordered table-hover text-center">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->id }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td>
                        <!-- Botón Ver -->
                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                data-target="#showTipoModal{{ $tipo->id }}">
                            <i class="fa fa-eye"></i> Ver
                        </button>

                        <!-- Botón Editar -->
                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#editTipoModal{{ $tipo->id }}">
                            <i class="fa fa-edit"></i> Editar
                        </button>

                        <!-- Botón Eliminar -->
                        <form action="{{ route('tipos.destroy', $tipo->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Desea eliminar este tipo?');">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Ver -->
                <div class="modal fade" id="showTipoModal{{ $tipo->id }}" tabindex="-1" role="dialog" aria-labelledby="showTipoLabel{{ $tipo->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content shadow-lg border-0">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="showTipoLabel{{ $tipo->id }}">
                                    <i class="fa fa-eye"></i> Detalles del Tipo
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>ID:</strong> {{ $tipo->id }}</li>
                                    <li class="list-group-item"><strong>Nombre:</strong> {{ $tipo->nombre }}</li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Editar -->
                <div class="modal fade" id="editTipoModal{{ $tipo->id }}" tabindex="-1" role="dialog" aria-labelledby="editTipoLabel{{ $tipo->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title" id="editTipoLabel{{ $tipo->id }}">
                                    <i class="fa fa-edit"></i> Editar Tipo
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('tipos.update', $tipo->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    @include('tipo.form', ['tipo' => $tipo])
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning text-white">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createTipoModal" tabindex="-1" role="dialog" aria-labelledby="createTipoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createTipoLabel">
                    <i class="fa fa-plus-circle"></i> Crear Tipo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('tipos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('tipo.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
