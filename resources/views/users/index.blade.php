@extends('adminlte::page')

@section('content')
<div class="row mb-3 d-flex justify-content-between align-items-center">
    <div class="col-auto">
        <h2>Gestión de Usuarios</h2>
    </div>
    @can('crear-usuario')
    <div class="col-auto">
        <a class="btn btn-success btn-sm" href="{{ route('users.create') }}">
            <i class="fa fa-plus"></i> Crear Nuevo Usuario
        </a>
    </div>
    @endcan
</div>

{{-- Mensaje de éxito --}}
@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nombre</th>
                    <th>Correo Electrónico</th>
                    <th>Roles</th>
                    <th width="280px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $user)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <span class="badge bg-success me-1">{{ $v }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @can('ver-usuario')
                        <a class="btn btn-info btn-sm" title="Ver" href="{{ route('users.show',$user->id) }}">
                            <i class="fa fa-eye"></i> 
                        </a>
                        @endcan
                        @can('editar-usuario')
                        <a class="btn btn-primary btn-sm" title="Editar" href="{{ route('users.edit',$user->id) }}">
                            <i class="fa fa-edit"></i> 
                        </a>
                        @endcan
                        @can('borrar-usuario')
                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Desea eliminar este usuario?');">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-2">
    {!! $data->links('pagination::bootstrap-5') !!}
</div>

<p class="text-center text-primary mt-3"><small>UPDS</small></p>
@endsection

@section('css')
    {{-- CSS personalizado si se requiere --}}
@stop

@section('js')
    <script>
        console.log("Gestión de Usuarios cargada con layouts.app");
    </script>
@stop
