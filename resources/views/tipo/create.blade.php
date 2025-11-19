@extends('adminlte::page')

@section('title', 'Crear Tipo de Producto')

@section('content_header')
<h1><i class="fas fa-plus-circle"></i> Crear Tipo de Producto</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tipos.store') }}" method="POST">
            @csrf
            @include('tipo.form')
            <div class="mt-3">
                <a href="{{ route('tipos.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@stop
