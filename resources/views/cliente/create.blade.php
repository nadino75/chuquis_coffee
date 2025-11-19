<div class="modal fade" id="createClienteModal" tabindex="-1" role="dialog" aria-labelledby="createClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createClienteModalLabel">
                    <i class="fas fa-plus-circle mr-2"></i>Nuevo Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ci" class="font-weight-bold">CI <span class="text-danger">*</span></label>
                                <input type="text" name="ci" id="ci" value="{{ old('ci') }}" 
                                       class="form-control @error('ci') is-invalid @enderror" 
                                       placeholder="Ingrese CI (12 caracteres)" maxlength="12" required>
                                @error('ci')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NIT" class="font-weight-bold">NIT</label>
                                <input type="text" name="NIT" id="NIT" value="{{ old('NIT') }}" 
                                       class="form-control @error('NIT') is-invalid @enderror" 
                                       placeholder="Ingrese NIT (13 caracteres)" maxlength="13">
                                @error('NIT')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombres" class="font-weight-bold">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" id="nombres" value="{{ old('nombres') }}" 
                                       class="form-control @error('nombres') is-invalid @enderror" 
                                       placeholder="Ingrese nombres" required>
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="apellido_paterno" class="font-weight-bold">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno') }}" 
                                       class="form-control @error('apellido_paterno') is-invalid @enderror" 
                                       placeholder="Ingrese apellido paterno">
                                @error('apellido_paterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="apellido_materno" class="font-weight-bold">Apellido Materno</label>
                                <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}" 
                                       class="form-control @error('apellido_materno') is-invalid @enderror" 
                                       placeholder="Ingrese apellido materno">
                                @error('apellido_materno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sexo" class="font-weight-bold">Sexo</label>
                                <select name="sexo" id="sexo" class="form-control @error('sexo') is-invalid @enderror">
                                    <option value="">Seleccione sexo</option>
                                    <option value="masculino" {{ old('sexo') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('sexo') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                                @error('sexo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefono" class="font-weight-bold">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       placeholder="Ingrese teléfono" maxlength="10">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="celular" class="font-weight-bold">Celular <span class="text-danger">*</span></label>
                                <input type="text" name="celular" id="celular" value="{{ old('celular') }}" 
                                       class="form-control @error('celular') is-invalid @enderror" 
                                       placeholder="Ingrese celular" maxlength="10" required>
                                @error('celular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="correo" class="font-weight-bold">Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="correo" id="correo" value="{{ old('correo') }}" 
                                   class="form-control @error('correo') is-invalid @enderror" 
                                   placeholder="ejemplo@correo.com">
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
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>