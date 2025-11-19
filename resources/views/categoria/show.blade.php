<!-- Modal Ver Categoría -->
<div class="modal fade" id="showCategoriaModal{{ $categoria->id }}" tabindex="-1" role="dialog" aria-labelledby="showCategoriaLabel{{ $categoria->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showCategoriaLabel{{ $categoria->id }}">
                    <i class="fa fa-eye"></i> Detalles de la Categoría
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>ID:</strong> {{ $categoria->id }}</li>
                    <li class="list-group-item"><strong>Nombre:</strong> {{ $categoria->nombre }}</li>
                    <li class="list-group-item"><strong>Descripción:</strong> {{ $categoria->descripcion }}</li>
                    <li class="list-group-item"><strong>Tipo:</strong> {{ $categoria->tipo->nombre ?? '-' }}</li>
                    <li class="list-group-item"><strong>Categoria Padre:</strong> {{ $categoria->categoria_padre->nombre ?? '-' }}</li>
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
