<div class="form-row">
    <div class="form-group col-md-6">
        <label>Proveedor</label>
        <select name="proveedore_id" class="form-control" required>
            <option value="">-- Seleccionar --</option>
            @foreach($proveedores as $proveedor)
                <option value="{{ $proveedor->id }}"
                    {{ (isset($proveedoresProducto) && $proveedoresProducto->proveedore_id == $proveedor->id) ? 'selected' : '' }}>
                    {{ $proveedor->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Producto</label>
        <select name="producto_id" class="form-control" required>
            <option value="">-- Seleccionar --</option>
            @foreach($productos as $producto)
                <option value="{{ $producto->id }}"
                    {{ (isset($proveedoresProducto) && $proveedoresProducto->producto_id == $producto->id) ? 'selected' : '' }}>
                    {{ $producto->nombre }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>Marca</label>
        <select name="marca_id" class="form-control" required>
            <option value="">-- Seleccionar --</option>
            @foreach($marcas as $marca)
                <option value="{{ $marca->id }}"
                    {{ (isset($proveedoresProducto) && $proveedoresProducto->marca_id == $marca->id) ? 'selected' : '' }}>
                    {{ $marca->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Cantidad</label>
        <input type="number" name="cantidad" value="{{ $proveedoresProducto->cantidad ?? old('cantidad') }}" class="form-control" required>
    </div>
    <div class="form-group col-md-4">
        <label>Unidad</label>
        <select name="unidad" class="form-control" required>
            @foreach($unidades as $unidad)
                <option value="{{ $unidad }}" {{ (isset($proveedoresProducto) && $proveedoresProducto->unidad == $unidad) ? 'selected' : '' }}>
                    {{ ucfirst($unidad) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>Precio</label>
        <input type="number" name="precio" value="{{ $proveedoresProducto->precio ?? old('precio') }}" step="0.01" class="form-control" required>
    </div>
    <div class="form-group col-md-4">
        <label>Fecha Compra</label>
        <input type="date" name="fecha_compra" value="{{ $proveedoresProducto->fecha_compra ?? old('fecha_compra') }}" class="form-control" required>
    </div>
    <div class="form-group col-md-4">
        <label>Fecha Vencimiento</label>
        <input type="date" name="fecha_vencimiento" value="{{ $proveedoresProducto->fecha_vencimiento ?? old('fecha_vencimiento') }}" class="form-control">
    </div>
</div>
