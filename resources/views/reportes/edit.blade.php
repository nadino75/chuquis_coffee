@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Reporte: {{ $reporte->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('reportes.update', $reporte) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Reporte *</label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre', $reporte->nombre) }}" 
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Reporte *</label>
                                    <select class="form-control @error('tipo') is-invalid @enderror" 
                                            id="tipo" 
                                            name="tipo" 
                                            required
                                            {{ $reporte->estado == 'procesando' ? 'disabled' : '' }}>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="ventas" {{ old('tipo', $reporte->tipo) == 'ventas' ? 'selected' : '' }}>Ventas</option>
                                        <option value="pagos" {{ old('tipo', $reporte->tipo) == 'pagos' ? 'selected' : '' }}>Pagos</option>
                                        <option value="productos" {{ old('tipo', $reporte->tipo) == 'productos' ? 'selected' : '' }}>Productos</option>
                                        <option value="inventario" {{ old('tipo', $reporte->tipo) == 'inventario' ? 'selected' : '' }}>Inventario</option>
                                        <option value="clientes" {{ old('tipo', $reporte->tipo) == 'clientes' ? 'selected' : '' }}>Clientes</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3">{{ old('descripcion', $reporte->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select class="form-control @error('estado') is-invalid @enderror" 
                                    id="estado" 
                                    name="estado"
                                    required>
                                <option value="pendiente" {{ old('estado', $reporte->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="procesando" {{ old('estado', $reporte->estado) == 'procesando' ? 'selected' : '' }}>Procesando</option>
                                <option value="completado" {{ old('estado', $reporte->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="error" {{ old('estado', $reporte->estado) == 'error' ? 'selected' : '' }}>Error</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($reporte->parametros)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Parámetros del Reporte</h6>
                            <pre class="mb-0">{{ json_encode($reporte->parametros, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" 
                                {{ $reporte->estado == 'procesando' ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i> Actualizar Reporte
                        </button>
                        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Cancelar</a>
                        
                        @if($reporte->estado == 'completado' && $reporte->archivo_ruta)
                        <a href="{{ route('reportes.download', $reporte) }}" class="btn btn-success float-right">
                            <i class="fas fa-download"></i> Descargar Reporte
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection