@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Gestión de Roles</h2>
        </div>
        <div class="pull-right">
        @can('crear-rol')
            <a class="btn btn-success btn-sm mb-2" href="{{ route('roles.create') }}">
                <i class="fa fa-plus"></i> Crear Nuevo Rol
            </a>
        @endcan
        </div>
    </div>
</div>

@session('success')
    <div class="alert alert-success" role="alert"> 
        {{ $value }}
    </div>
@endsession

<table class="table table-bordered">
  <tr>
     <th width="100px">N°</th>
     <th>Nombre del Rol</th>
     <th width="280px">Acciones</th>
  </tr>

    @foreach ($roles as $key => $role)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $role->name }}</td>
        <td>
            <a class="btn btn-info btn-sm" title="Ver" href="{{ route('roles.show', $role->id) }}">
                <i class="fa fa-eye"></i> 
            </a>

            @can('editar-rol')
                <a class="btn btn-primary btn-sm" title="Editar" href="{{ route('roles.edit', $role->id) }}">
                    <i class="fa fa-edit"></i> 
                </a>
            @endcan

            @can('borrar-rol')
            <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display:inline">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger btn-sm"  title="Eliminar">
                    <i class="fa fa-trash"></i> 
                </button>
            </form>
            @endcan
        </td>
    </tr>
    @endforeach
</table>

{!! $roles->links('pagination::bootstrap-5') !!}

<p class="text-center text-primary">
    <small>UPDS</small>
</p>
@endsection
