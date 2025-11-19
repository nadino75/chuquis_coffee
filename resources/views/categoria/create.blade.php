<!-- Modal Crear Categoría -->
<div class="modal fade" id="createCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="createCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createCategoriaLabel">
                    <i class="fa fa-plus-circle"></i> Crear Categoría
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('categoria.form', ['tipos' => $tipos, 'categoriasPadre' => $categoriasPadre])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
