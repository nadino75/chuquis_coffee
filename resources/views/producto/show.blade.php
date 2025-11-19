<div class="modal fade" id="showProductoModal{{ $producto->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5>Detalles del Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
                <p><strong>Stock:</strong> {{ $producto->stock }}</p>
                <p><strong>Precio:</strong> {{ $producto->precio }}</p>
                <p><strong>Categoria:</strong> {{ $producto->categoria->nombre }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
