<div class="form-group">
    <label>Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="form-control">
    @error('nombre')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Stock</label>
    <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? '') }}" class="form-control">
    @error('stock')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Precio</label>
    <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio ?? '') }}" class="form-control">
    @error('precio')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Categoria</label>
    <select name="categoria_id" class="form-control">
        <option value="">-- Seleccionar Categoria --</option>
        @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}" 
                {{ (isset($producto) && $producto->categoria_id == $categoria->id) ? 'selected' : '' }}>
                {{ $categoria->nombre }}
            </option>
        @endforeach
    </select>
    @error('categoria_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
