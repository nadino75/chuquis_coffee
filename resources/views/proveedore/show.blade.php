<div class="modal fade" id="showProveedoreModal{{ $proveedore->id }}" tabindex="-1" role="dialog" aria-labelledby="showProveedoreLabel{{ $proveedore->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="showProveedoreLabel{{ $proveedore->id }}">
          <i class="fa fa-eye"></i> Detalles del Proveedore
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><strong>ID:</strong> {{ $proveedore->id }}</li>
          <li class="list-group-item"><strong>Nombre:</strong> {{ $proveedore->nombre }}</li>
          <li class="list-group-item"><strong>Dirección:</strong> {{ $proveedore->direccion ?? '—' }}</li>
          <li class="list-group-item"><strong>Teléfono:</strong> {{ $proveedore->telefono ?? '—' }}</li>
          <li class="list-group-item"><strong>Celular:</strong> {{ $proveedore->celular }}</li>
          <li class="list-group-item"><strong>Correo:</strong> {{ $proveedore->correo ?? '—' }}</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
