@extends('adminlte::page')

@section('title', 'Proveedore')

@section('content_header')
<h1><i class="fas fa-truck"></i> Proveedore</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">Lista de Proveedore</h4>
        <!-- Botón Crear Nuevo a la derecha -->
        <div class="ml-auto">
            @can('crear-proveedor')
            <button type="button" class="btn btn-light btn-sm" 
                    data-toggle="modal"
                    data-target="#createProveedoreModal">
                <i class="fa fa-plus-circle"></i> Crear Nuevo
            </button>
            @endcan
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover text-center">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proveedores as $proveedore)
                <tr>
                    <td>{{ $proveedore->id }}</td>
                    <td>{{ $proveedore->nombre }}</td>
                    <td>{{ $proveedore->direccion }}</td>
                    <td>{{ $proveedore->telefono }}</td>
                    <td>{{ $proveedore->celular }}</td>
                    <td>{{ $proveedore->correo }}</td>
                    <td>
                        <!-- Botón Ver -->
                        @can('ver-proveedor')
                        <button class="btn btn-info btn-sm"  title="Ver" data-toggle="modal"
                            data-target="#showProveedoreModal{{ $proveedore->id }}">
                            <i class="fa fa-eye"></i> 
                        </button>
                        @endcan
                        <!-- Botón Editar -->
                        @can('editar-proveedor')
                        <button class="btn btn-warning btn-sm"  title="Editar" data-toggle="modal"
                            data-target="#editProveedoreModal{{ $proveedore->id }}">
                            <i class="fa fa-edit"></i> 
                        </button>
                        @endcan

                        <!-- Botón Eliminar -->
                        @can('borrar-proveedor')
                        <form action="{{ route('proveedores.destroy', $proveedore->id) }}" method="POST"  style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"  title="Eliminar"
                                onclick="return confirm('¿Desea eliminar este proveedor?');">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>

                <!-- Modal Ver -->
                <div class="modal fade" id="showProveedoreModal{{ $proveedore->id }}" tabindex="-1" aria-labelledby="showProveedoreLabel{{ $proveedore->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg border-0">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="showProveedoreLabel{{ $proveedore->id }}">
                                    <i class="fa fa-eye"></i> Detalles del Proveedor
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>ID:</strong> {{ $proveedore->id }}</li>
                                    <li class="list-group-item"><strong>Nombre:</strong> {{ $proveedore->nombre }}</li>
                                    <li class="list-group-item"><strong>Dirección:</strong> {{ $proveedore->direccion ?? '—' }}</li>
                                    <li class="list-group-item"><strong>Teléfono:</strong> {{ $proveedore->telefono ?? '—' }}</li>
                                    <li class="list-group-item"><strong>Celular:</strong> {{ $proveedore->celular }}</li>
                                    <li class="list-group-item"><strong>Correo:</strong> {{ $proveedore->correo ?? '—' }}</li>
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
                <div class="modal fade" id="editProveedoreModal{{ $proveedore->id }}" tabindex="-1" aria-labelledby="editProveedoreLabel{{ $proveedore->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title" id="editProveedoreLabel{{ $proveedore->id }}">
                                    <i class="fa fa-edit"></i> Editar Proveedor
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('proveedores.update', $proveedore->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    @include('proveedore.form', ['proveedore' => $proveedore])
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fa fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-warning text-white">
                                        <i class="fa fa-save"></i> Actualizar
                                    </button>
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
<div class="modal fade" id="createProveedoreModal" tabindex="-1" aria-labelledby="createProveedoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createProveedoreLabel">
                    <i class="fa fa-plus-circle"></i> Crear Proveedore
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('proveedore.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Resetea los formularios al cerrar los modales
    $(document).ready(function(){
        $('.modal').on('hidden.bs.modal', function () {
            var form = $(this).find('form')[0];
            if(form) form.reset();
        });
    });
</script>
@stop
