<!-- Modal Editar Categoría -->
<div class="modal fade" id="editCategoriaModal{{ $categoria->id }}" tabindex="-1" role="dialog" aria-labelledby="editCategoriaLabel{{ $categoria->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editCategoriaLabel{{ $categoria->id }}">
                    <i class="fa fa-edit"></i> Editar Categoría
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('categoria.form', ['categoria' => $categoria, 'tipos' => $tipos, 'categoriasPadre' => $categoriasPadre])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
