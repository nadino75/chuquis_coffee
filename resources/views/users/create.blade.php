@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between align-items-center">
        <h2>Crear Nuevo Usuario</h2>
        <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger mt-2">
      <strong>¡Ups!</strong> Hubo algunos problemas con los datos ingresados.<br><br>
      <ul>
         @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
         @endforeach
      </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="form-group mb-2">
                <strong>Nombre:</strong>
                <input type="text" name="name" placeholder="Nombre" class="form-control">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-2">
                <strong>Correo Electrónico:</strong>
                <input type="email" name="email" placeholder="Correo Electrónico" class="form-control">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-2">
                <strong>Contraseña:</strong>
                <input type="password" name="password" placeholder="Contraseña" class="form-control">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-2">
                <strong>Confirmar Contraseña:</strong>
                <input type="password" name="confirm-password" placeholder="Confirmar Contraseña" class="form-control">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group mb-3">
                <strong>Rol:</strong>
                <select name="roles[]" class="form-control" multiple="multiple">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                     @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i> Guardar
            </button>
        </div>
    </div>
</form>

<p class="text-center text-primary mt-3"><small>UPDS</small></p>
@endsection
