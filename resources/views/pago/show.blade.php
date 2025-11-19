@extends('adminlte::page')

@section('template_title')
    {{ $pago->name ?? __('Show') . " " . __('Pago') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Pago</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('pagos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Recibo:</strong>
                                    {{ $pago->recibo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha:</strong>
                                    {{ $pago->fecha }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo Pago:</strong>
                                    {{ $pago->tipo_pago }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total Pagado:</strong>
                                    {{ $pago->total_pagado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cliente Ci:</strong>
                                    {{ $pago->cliente_ci }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
