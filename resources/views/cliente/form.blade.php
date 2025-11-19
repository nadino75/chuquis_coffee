<div class="form-group">
    <label>CI</label>
    <input type="text" name="ci" value="{{ old('ci', $cliente?->ci) }}" class="form-control">
</div>
<div class="form-group">
    <label>Nombres</label>
    <input type="text" name="nombres" value="{{ old('nombres', $cliente?->nombres) }}" class="form-control">
</div>
<div class="form-group">
    <label>Apellido Paterno</label>
    <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $cliente?->apellido_paterno) }}" class="form-control">
</div>
<div class="form-group">
    <label>Apellido Materno</label>
    <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $cliente?->apellido_materno) }}" class="form-control">
</div>
<div class="form-group">
    <label>Dirección</label>
    <input type="text" name="direccion" value="{{ old('direccion', $cliente?->direccion) }}" class="form-control">
</div>
<div class="form-group">
    <label>Teléfono</label>
    <input type="text" name="telefono" value="{{ old('telefono', $cliente?->telefono) }}" class="form-control">
</div>
<div class="form-group">
    <label>Email</label>
    <input type="email" name="correo" value="{{ old('correo', $cliente?->correo) }}" class="form-control">
</div>
