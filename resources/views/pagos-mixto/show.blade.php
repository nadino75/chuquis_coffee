@extends('layouts.app')

@section('template_title')
    {{ $pagosMixto->name ?? __('Show') . " " . __('Pagos Mixto') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Pagos Mixto</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('pagos-mixtos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo Pago:</strong>
                                    {{ $pagosMixto->tipo_pago }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Monto:</strong>
                                    {{ $pagosMixto->monto }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Pago Id:</strong>
                                    {{ $pagosMixto->pago_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
