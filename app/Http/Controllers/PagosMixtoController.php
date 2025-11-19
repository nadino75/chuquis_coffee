<?php

namespace App\Http\Controllers;

use App\Models\PagosMixto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PagosMixtoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PagosMixtoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pagosMixtos = PagosMixto::paginate();

        return view('pagos-mixto.index', compact('pagosMixtos'))
            ->with('i', ($request->input('page', 1) - 1) * $pagosMixtos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pagosMixto = new PagosMixto();

        return view('pagos-mixto.create', compact('pagosMixto'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PagosMixtoRequest $request): RedirectResponse
    {
        PagosMixto::create($request->validated());

        return Redirect::route('pagos-mixtos.index')
            ->with('success', 'PagosMixto created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $pagosMixto = PagosMixto::find($id);

        return view('pagos-mixto.show', compact('pagosMixto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $pagosMixto = PagosMixto::find($id);

        return view('pagos-mixto.edit', compact('pagosMixto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PagosMixtoRequest $request, PagosMixto $pagosMixto): RedirectResponse
    {
        $pagosMixto->update($request->validated());

        return Redirect::route('pagos-mixtos.index')
            ->with('success', 'PagosMixto updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        PagosMixto::find($id)->delete();

        return Redirect::route('pagos-mixtos.index')
            ->with('success', 'PagosMixto deleted successfully');
    }
}
