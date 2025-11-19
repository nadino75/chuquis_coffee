{{-- resources/views/tipo/form.blade.php --}}
<div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" class="form-control" id="nombre"
        value="{{ old('nombre', $tipo->nombre ?? '') }}" placeholder="Ingrese el nombre del tipo de producto" required>
</div>
