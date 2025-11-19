<!-- Modal Crear Proveedores -->
<div class="modal fade" id="createProveedoreModal" tabindex="-1" role="dialog" aria-labelledby="createProveedoreLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <!-- Cabecera del Modal -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="createProveedoreLabel">
          <i class="fa fa-plus-circle"></i> Crear Proveedore
        </h5>
        <!-- Botón Cerrar -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Cuerpo del Modal con Formulario -->
      <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          @include('proveedore.form')
        </div>

        <!-- Footer del Modal -->
        <div class="modal-footer">
          <!-- Botón Cancelar -->
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancelar
          </button>
          <!-- Botón Guardar -->
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Guardar
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
