<div class="modal fade" id="editMarcaModal{{ $marca->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5>Editar Marca</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('marcas.update', $marca->id) }}">
                    @csrf
                    @method('PATCH')
                    @include('marcas.form', ['marca' => $marca])
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
