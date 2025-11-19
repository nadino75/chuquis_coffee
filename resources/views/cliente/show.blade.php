<div class="modal fade" id="showClienteModal{{ $cliente->ci }}" tabindex="-1" role="dialog" aria-labelledby="showClienteModalLabel{{ $cliente->ci }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showClienteModalLabel{{ $cliente->ci }}">
                    <i class="fas fa-eye mr-2"></i>Detalles del Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">CI:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->ci }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">NIT:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->NIT ?? 'No registrado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">Nombres:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->nombres }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">Apellido Paterno:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->apellido_paterno ?? 'No registrado' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">Apellido Materno:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->apellido_materno ?? 'No registrado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">Sexo:</label>
                            <p class="form-control-plaintext border-bottom pb-1">
                                @if($cliente->sexo)
                                    {{ ucfirst($cliente->sexo) }}
                                @else
                                    No registrado
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">Teléfono:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->telefono ?? 'No registrado' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="font-weight-bold text-primary">Celular:</label>
                            <p class="form-control-plaintext border-bottom pb-1">{{ $cliente->celular }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-item mb-3">
                    <label class="font-weight-bold text-primary">Email:</label>
                    <p class="form-control-plaintext border-bottom pb-1">
                        @if($cliente->correo)
                            <i class="fas fa-envelope mr-1 text-muted"></i>{{ $cliente->correo }}
                        @else
                            No registrado
                        @endif
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>