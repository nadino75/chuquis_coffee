<div class="modal fade" id="showMarcaModal{{ $marca->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5>Detalles de Marca</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> {{ $marca->id }}</p>
                <p><strong>Nombre:</strong> {{ $marca->nombre }}</p>
                <p><strong>Creado:</strong> {{ $marca->created_at }}</p>
                <p><strong>Actualizado:</strong> {{ $marca->updated_at }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
