@extends('adminlte::page')

@section('title', 'Detalles del Tipo')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4>Detalles del Tipo</h4>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>ID:</strong> {{ $tipo->id }}</li>
                <li class="list-group-item"><strong>Nombre:</strong> {{ $tipo->nombre }}</li>
            </ul>
        </div>
        <div class="card-footer">
            <a href="{{ route('tipos.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Volver</a>
        </div>
    </div>
</div>
@stop
