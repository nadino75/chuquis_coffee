{{-- resources/views/tipo/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Editar Tipo de Producto')

@section('content_header')
<h1><i class="fas fa-edit"></i> Editar Tipo de Producto</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tipos.update', $tipo->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('tipo.form', ['tipo' => $tipo])
            <div class="mt-3">
                <a href="{{ route('tipos.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="btn btn-warning text-white"><i class="fa fa-edit"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>
@stop
