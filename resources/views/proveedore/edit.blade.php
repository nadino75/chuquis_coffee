<div class="modal fade" id="editProveedoreModal{{ $proveedore->id }}" tabindex="-1" role="dialog" aria-labelledby="editProveedoreLabel{{ $proveedore->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="editProveedoreLabel{{ $proveedore->id }}">
          <i class="fa fa-edit"></i> Editar Proveedore
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('proveedores.update', $proveedore->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          @include('proveedore.form', ['proveedore' => $proveedore])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-warning text-white">
            <i class="fa fa-save"></i> Actualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
