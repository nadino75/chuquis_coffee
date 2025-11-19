<!-- Modal Show -->
<div class="modal fade" id="showProveedoresProductoModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="showModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showModalLabel{{ $item->id }}">
                    Ver Proveedor Producto
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr><th>Proveedor:</th><td>{{ $item->proveedore->nombre ?? '-' }}</td></tr>
                    <tr><th>Producto:</th><td>{{ $item->producto->nombre ?? '-' }}</td></tr>
                    <tr><th>Cantidad:</th><td>{{ $item->cantidad }}</td></tr>
                    <tr><th>Unidad:</th><td>{{ $item->unidad }}</td></tr>
                    <tr><th>Precio:</th><td>{{ $item->precio }}</td></tr>
                    <tr><th>Fecha Compra:</th><td>{{ $item->fecha_compra }}</td></tr>
                    <tr><th>Fecha Vencimiento:</th><td>{{ $item->fecha_vencimiento ?? '-' }}</td></tr>
                    <tr><th>Marca:</th><td>{{ $item->marca->nombre ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
