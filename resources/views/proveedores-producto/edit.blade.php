<!-- Modal Editar ProveedoresProducto -->
<div class="modal fade" id="editProveedoresProductoModal{{ $proveedoresProducto->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $proveedoresProducto->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ route('proveedores_productos.update', $proveedoresProducto->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="editModalLabel{{ $proveedoresProducto->id }}">Editar Proveedor Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            @include('proveedores-producto.form', [
                'proveedoresProducto' => $proveedoresProducto,
                'proveedores' => $proveedores,
                'productos' => $productos,
                'marcas' => $marcas,
                'unidades' => $unidades
            ])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-success">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>
