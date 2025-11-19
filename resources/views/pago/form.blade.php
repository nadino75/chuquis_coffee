<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="recibo" class="form-label">{{ __('Recibo') }}</label>
            <input type="text" name="recibo" class="form-control @error('recibo') is-invalid @enderror" value="{{ old('recibo', $pago?->recibo) }}" id="recibo" placeholder="Recibo">
            {!! $errors->first('recibo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha" class="form-label">{{ __('Fecha') }}</label>
            <input type="text" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $pago?->fecha) }}" id="fecha" placeholder="Fecha">
            {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_pago" class="form-label">{{ __('Tipo Pago') }}</label>
            <input type="text" name="tipo_pago" class="form-control @error('tipo_pago') is-invalid @enderror" value="{{ old('tipo_pago', $pago?->tipo_pago) }}" id="tipo_pago" placeholder="Tipo Pago">
            {!! $errors->first('tipo_pago', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="total_pagado" class="form-label">{{ __('Total Pagado') }}</label>
            <input type="text" name="total_pagado" class="form-control @error('total_pagado') is-invalid @enderror" value="{{ old('total_pagado', $pago?->total_pagado) }}" id="total_pagado" placeholder="Total Pagado">
            {!! $errors->first('total_pagado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cliente_ci" class="form-label">{{ __('Cliente Ci') }}</label>
            <input type="text" name="cliente_ci" class="form-control @error('cliente_ci') is-invalid @enderror" value="{{ old('cliente_ci', $pago?->cliente_ci) }}" id="cliente_ci" placeholder="Cliente Ci">
            {!! $errors->first('cliente_ci', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>