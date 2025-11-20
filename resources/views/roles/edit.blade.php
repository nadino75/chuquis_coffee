@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Editar Rol</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('roles.index') }}">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>¡Ups!</strong> Hubo algunos problemas con tu entrada.<br><br>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf
    @method('PUT')

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group mb-2">
                <strong>Nombre del Rol:</strong>
                <input type="text" name="name" placeholder="Nombre del rol" class="form-control" value="{{ $role->name }}">
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group mb-3">
                <strong>Permisos:</strong>
                <br/>
                @foreach($permission as $value)
                    <label>
                        <input 
                            type="checkbox" 
                            name="permission[{{$value->id}}]" 
                            value="{{$value->id}}" 
                            class="name"
                            {{ in_array($value->id, $rolePermissions) ? 'checked' : ''}}
                        >
                        {{ $value->name }}
                    </label>
                    <br/>
                @endforeach
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mb-3">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
            </button>
        </div>

    </div>
</form>

<p class="text-center text-primary"><small>UPDS</small></p>
@endsection
