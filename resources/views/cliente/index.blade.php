@extends('adminlte::page')

@section('title', 'Gestión de Clientes')

@section('content_header')
    <h1 class="d-flex align-items-center">
        <i class="fas fa-users fa-lg mr-2"></i>
        Gestión de Clientes
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary d-flex align-items-center">
            <h5 class="card-title mb-0 text-white flex-grow-1">
                <i class="fas fa-list mr-1"></i> Lista de Clientes
            </h5>
            <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createClienteModal">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo Cliente
            </button>
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th width="15%">CI</th>
                            <th>Nombre Completo</th>
                            <th width="12%">Celular</th>
                            <th width="15%">Email</th>
                            <th width="20%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($clientes->currentPage() - 1) * $clientes->perPage() }}</td>
                                <td class="text-center"><strong>{{ $cliente->ci }}</strong></td>
                                <td>
                                    {{ $cliente->nombres }} {{ $cliente->apellido_paterno }} 
                                    {{ $cliente->apellido_materno ? $cliente->apellido_materno : '' }}
                                </td>
                                <td class="text-center">{{ $cliente->celular }}</td>
                                <td>{{ $cliente->correo ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Botón Ver -->
                                        <button class="btn btn-info btn-sm" data-toggle="modal" 
                                                data-target="#showClienteModal{{ $cliente->ci }}">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>

                                        <!-- Botón Editar -->
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" 
                                                data-target="#editClienteModal{{ $cliente->ci }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <!-- Botón Eliminar -->
                                        <form action="{{ route('clientes.destroy', $cliente->ci) }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este cliente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2"></i><br>
                                    No hay clientes registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clientes->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} registros
                    </div>
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Create -->
    @include('cliente.create')  <!-- Cambiado a 'cliente.create' -->

    <!-- Modales Show y Edit -->
    @foreach($clientes as $cliente)
        @include('cliente.show', ['cliente' => $cliente])  <!-- Cambiado a 'cliente.show' -->
        @include('cliente.edit', ['cliente' => $cliente])  <!-- Cambiado a 'cliente.edit' -->
    @endforeach
@stop

@section('css')
    <style>
        .table th {
            font-weight: 600;
            font-size: 0.85rem;
        }
        .btn-group .btn {
            margin: 0 2px;
        }
        .card-header {
            border-bottom: 2px solid #dee2e6;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Resetea formularios al cerrar modales
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form').trigger('reset');
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
            });

            // Auto-close alerts
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
@stop