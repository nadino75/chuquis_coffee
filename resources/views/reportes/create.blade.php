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
                                        <option value="ventas" {{ old('tipo') == 'ventas' ? 'selected' : '' }}>Ventas</option>
                                        <option value="pagos" {{ old('tipo') == 'pagos' ? 'selected' : '' }}>Pagos</option>
                                        <option value="productos" {{ old('tipo') == 'productos' ? 'selected' : '' }}>Productos</option>
                                        <option value="inventario" {{ old('tipo') == 'inventario' ? 'selected' : '' }}>Inventario</option>
                                        <option value="clientes" {{ old('tipo') == 'clientes' ? 'selected' : '' }}>Clientes</option>
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

                        <!-- Campos específicos según el tipo de reporte -->
                        <div id="campos-ventas" class="campos-tipo" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="venta_id">Venta Específica</label>
                                        <select class="form-control" id="venta_id" name="venta_id">
                                            <option value="">Todas las ventas</option>
                                            <!-- Aquí puedes cargar las ventas desde la base de datos -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="campos-productos" class="campos-tipo" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="producto_id">Producto Específico</label>
                                        <select class="form-control" id="producto_id" name="producto_id">
                                            <option value="">Todos los productos</option>
                                            <!-- Aquí puedes cargar los productos desde la base de datos -->
                                        </select>
                                    </div>
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

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const camposTipo = document.querySelectorAll('.campos-tipo');

        function mostrarCamposTipo() {
            // Ocultar todos los campos primero
            camposTipo.forEach(campo => {
                campo.style.display = 'none';
            });

            // Mostrar campos según el tipo seleccionado
            const tipoSeleccionado = tipoSelect.value;
            if (tipoSeleccionado) {
                const campos = document.getElementById(`campos-${tipoSeleccionado}`);
                if (campos) {
                    campos.style.display = 'block';
                }
            }
        }

        // Event listener para cambios en el select de tipo
        tipoSelect.addEventListener('change', mostrarCamposTipo);

        // Mostrar campos iniciales si hay un valor en old
        @if(old('tipo'))
        mostrarCamposTipo();
        @endif

        // Validación de fechas
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');

        if (fechaInicio && fechaFin) {
            fechaInicio.addEventListener('change', function() {
                if (this.value) {
                    fechaFin.min = this.value;
                }
            });

            fechaFin.addEventListener('change', function() {
                if (this.value && fechaInicio.value && this.value < fechaInicio.value) {
                    alert('La fecha fin no puede ser anterior a la fecha inicio');
                    this.value = '';
                }
            });
        }
    });
</script>
@endsection