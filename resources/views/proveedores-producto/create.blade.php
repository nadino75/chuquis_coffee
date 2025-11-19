<!-- Modal Crear ProveedoresProducto -->
<div class="modal fade" id="createProveedoresProductoModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ route('proveedores_productos.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="createModalLabel">Crear Proveedor Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            @include('proveedores-producto.form', [
                'proveedoresProducto' => new App\Models\ProveedoresProducto(),
                'proveedores' => $proveedores,
                'productos' => $productos,
                'marcas' => $marcas,
                'unidades' => $unidades
            ])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>
