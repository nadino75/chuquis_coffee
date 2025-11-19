<div class="modal fade" id="editClienteModal{{ $cliente->ci }}" tabindex="-1" role="dialog" aria-labelledby="editClienteModalLabel{{ $cliente->ci }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editClienteModalLabel{{ $cliente->ci }}">
                    <i class="fas fa-edit mr-2"></i>Editar Cliente
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('clientes.update', $cliente->ci) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ci_{{ $cliente->ci }}" class="font-weight-bold">CI <span class="text-danger">*</span></label>
                                <input type="text" name="ci" id="edit_ci_{{ $cliente->ci }}" value="{{ old('ci', $cliente->ci) }}" 
                                       class="form-control @error('ci') is-invalid @enderror" maxlength="12" required>
                                @error('ci')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_NIT_{{ $cliente->ci }}" class="font-weight-bold">NIT</label>
                                <input type="text" name="NIT" id="edit_NIT_{{ $cliente->ci }}" value="{{ old('NIT', $cliente->NIT) }}" 
                                       class="form-control @error('NIT') is-invalid @enderror" maxlength="13">
                                @error('NIT')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_nombres_{{ $cliente->ci }}" class="font-weight-bold">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" id="edit_nombres_{{ $cliente->ci }}" value="{{ old('nombres', $cliente->nombres) }}" 
                                       class="form-control @error('nombres') is-invalid @enderror" required>
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_apellido_paterno_{{ $cliente->ci }}" class="font-weight-bold">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" id="edit_apellido_paterno_{{ $cliente->ci }}" value="{{ old('apellido_paterno', $cliente->apellido_paterno) }}" 
                                       class="form-control @error('apellido_paterno') is-invalid @enderror">
                                @error('apellido_paterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_apellido_materno_{{ $cliente->ci }}" class="font-weight-bold">Apellido Materno</label>
                                <input type="text" name="apellido_materno" id="edit_apellido_materno_{{ $cliente->ci }}" value="{{ old('apellido_materno', $cliente->apellido_materno) }}" 
                                       class="form-control @error('apellido_materno') is-invalid @enderror">
                                @error('apellido_materno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_sexo_{{ $cliente->ci }}" class="font-weight-bold">Sexo</label>
                                <select name="sexo" id="edit_sexo_{{ $cliente->ci }}" class="form-control @error('sexo') is-invalid @enderror">
                                    <option value="">Seleccione sexo</option>
                                    <option value="masculino" {{ old('sexo', $cliente->sexo) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('sexo', $cliente->sexo) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                                @error('sexo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_telefono_{{ $cliente->ci }}" class="font-weight-bold">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono_{{ $cliente->ci }}" value="{{ old('telefono', $cliente->telefono) }}" 
                                       class="form-control @error('telefono') is-invalid @enderror" maxlength="10">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_celular_{{ $cliente->ci }}" class="font-weight-bold">Celular <span class="text-danger">*</span></label>
                                <input type="text" name="celular" id="edit_celular_{{ $cliente->ci }}" value="{{ old('celular', $cliente->celular) }}" 
                                       class="form-control @error('celular') is-invalid @enderror" maxlength="10" required>
                                @error('celular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_correo_{{ $cliente->ci }}" class="font-weight-bold">Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="correo" id="edit_correo_{{ $cliente->ci }}" value="{{ old('correo', $cliente->correo) }}" 
                                   class="form-control @error('correo') is-invalid @enderror">
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Actualizar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>