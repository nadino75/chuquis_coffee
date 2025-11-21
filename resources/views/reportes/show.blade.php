@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Crear Nuevo Reporte</h3>
                    <div class="card-tools">
                        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('reportes.store') }}" method="POST">
                    @csrf
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
                                           value="{{ old('nombre') }}" 
                                           required
                                           placeholder="Ingrese el nombre del reporte">
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
                                            required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="ventas" {{ old('tipo') == 'ventas' ? 'selected' : '' }}>Reporte de Ventas</option>
                                        <option value="pagos" {{ old('tipo') == 'pagos' ? 'selected' : '' }}>Reporte de Pagos</option>
                                        <option value="productos" {{ old('tipo') == 'productos' ? 'selected' : '' }}>Reporte de Productos</option>
                                        <option value="inventario" {{ old('tipo') == 'inventario' ? 'selected' : '' }}>Reporte de Inventario</option>
                                        <option value="clientes" {{ old('tipo') == 'clientes' ? 'selected' : '' }}>Reporte de Clientes</option>
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
                                      rows="3"
                                      placeholder="Descripción opcional del reporte">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha Inicio</label>
                                    <input type="date" 
                                           class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                           id="fecha_inicio" 
                                           name="fecha_inicio" 
                                           value="{{ old('fecha_inicio') }}">
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha Fin</label>
                                    <input type="date" 
                                           class="form-control @error('fecha_fin') is-invalid @enderror" 
                                           id="fecha_fin" 
                                           name="fecha_fin" 
                                           value="{{ old('fecha_fin') }}">
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Filtros adicionales -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="usuario_id">Usuario</label>
                                    <select class="form-control" id="usuario_id" name="usuario_id">
                                        <option value="">Todos los usuarios</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cliente_id">Cliente</label>
                                    <select class="form-control" id="cliente_id" name="cliente_id">
                                        <option value="">Todos los clientes</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="producto_id">Producto</label>
                                    <select class="form-control" id="producto_id" name="producto_id">
                                        <option value="">Todos los productos</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Generar Reporte
                        </button>
                        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection