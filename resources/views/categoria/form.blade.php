<div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" value="{{ $categoria->nombre ?? old('nombre') }}" required>
</div>

<div class="form-group">
    <label for="descripcion">Descripción</label>
    <textarea class="form-control" name="descripcion" rows="2">{{ $categoria->descripcion ?? old('descripcion') }}</textarea>
</div>

<div class="form-group">
    <label for="tipo_id">Tipo de Producto</label>
    <select class="form-control" name="tipo_id" required>
        <option value="">Seleccione un tipo</option>
        @foreach($tipos as $tipo)
            <option value="{{ $tipo->id }}"
                @if(isset($categoria) && $categoria->tipo_id == $tipo->id) selected @endif>
                {{ $tipo->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="categoria_id">Categoria Padre</label>
    <select class="form-control" name="categoria_id">
        <option value="">Ninguna</option>
        @foreach($categoriasPadre as $catPadre)
            <option value="{{ $catPadre->id }}"
                @if(isset($categoria) && $categoria->categoria_id == $catPadre->id) selected @endif>
                {{ $catPadre->nombre }}
            </option>
        @endforeach
    </select>
</div>
