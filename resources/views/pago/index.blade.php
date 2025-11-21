@extends('adminlte::page')

@section('template_title')
    Pagos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Pagos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('pagos.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Recibo</th>
									<th >Fecha</th>
									<th >Tipo Pago</th>
									<th >Total Pagado</th>
									<th >Cliente Ci</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pagos as $pago)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $pago->recibo }}</td>
										<td >{{ $pago->fecha }}</td>
										<td >{{ $pago->tipo_pago }}</td>
										<td >{{ $pago->total_pagado }}</td>
										<td >{{ $pago->cliente_ci }}</td>

                                            <td>
                                                <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " title="Ver" href="{{ route('pagos.show', $pago->id) }}"><i class="fa fa-fw fa-eye"></i> </a>
                                                    <a class="btn btn-sm btn-success" title="Editar" href="{{ route('pagos.edit', $pago->id) }}"><i class="fa fa-fw fa-edit"></i> </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $pagos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
