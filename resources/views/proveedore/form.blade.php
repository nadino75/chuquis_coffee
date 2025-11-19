<div class="form-group mb-3">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $proveedore->nombre ?? '') }}" required>
</div>

<div class="form-group mb-3">
    <label for="direccion">Dirección</label>
    <textarea name="direccion" class="form-control">{{ old('direccion', $proveedore->direccion ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="telefono">Teléfono</label>
    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $proveedore->telefono ?? '') }}">
</div>

<div class="form-group mb-3">
    <label for="celular">Celular</label>
    <input type="text" name="celular" class="form-control" value="{{ old('celular', $proveedore->celular ?? '') }}" required>
</div>

<div class="form-group mb-3">
    <label for="correo">Correo</label>
    <input type="email" name="correo" class="form-control" value="{{ old('correo', $proveedore->correo ?? '') }}">
</div>
