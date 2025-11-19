<div class="modal fade" id="editProductoModal{{ $producto->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5>Editar Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('productos.update', $producto->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @include('producto.form', ['producto' => $producto])
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
