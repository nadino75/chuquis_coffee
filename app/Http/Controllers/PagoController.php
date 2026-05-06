<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PagoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function index(Request $request): View
    {
        $pagos = Pago::paginate();

        return view('pago.index', compact('pagos'))
            ->with('i', ($request->input('page', 1) - 1) * $pagos->perPage());
    }

    public function create(): View
    {
        $pago = new Pago();

        return view('pago.create', compact('pago'));
    }

    public function store(PagoRequest $request): RedirectResponse
    {
        Pago::create($request->validated());

        return Redirect::route('pagos.index')
            ->with('success', 'Pago creado correctamente.');
    }

    public function show(int $id): View
    {
        $pago = Pago::findOrFail($id);

        return view('pago.show', compact('pago'));
    }

    public function edit(int $id): View
    {
        $pago = Pago::findOrFail($id);

        return view('pago.edit', compact('pago'));
    }

    public function update(PagoRequest $request, Pago $pago): RedirectResponse
    {
        $pago->update($request->validated());

        return Redirect::route('pagos.index')
            ->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Pago::findOrFail($id)->delete();

        return Redirect::route('pagos.index')
            ->with('success', 'Pago eliminado correctamente.');
    }

    // API Methods
    public function indexApi(): JsonResponse
    {
        $pagos = Pago::paginate(10);
        return response()->json($pagos);
    }

    public function showApi(int $id): JsonResponse
    {
        $pago = Pago::findOrFail($id);
        return response()->json($pago);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recibo' => 'nullable|max:50',
            'fecha' => 'nullable|date',
            'tipo_pago' => 'nullable|in:efectivo,tarjeta,transferencia,qr,mixto',
            'total_pagado' => 'required|numeric|min:0',
            'cliente_ci' => 'nullable|max:12|exists:clientes,ci',
        ]);
        $pago = Pago::create($validated);
        return response()->json($pago, 201);
    }

    public function updateApi(Request $request, int $id): JsonResponse
    {
        $pago = Pago::findOrFail($id);
        $validated = $request->validate([
            'recibo' => 'nullable|max:50',
            'fecha' => 'nullable|date',
            'tipo_pago' => 'nullable|in:efectivo,tarjeta,transferencia,qr,mixto',
            'total_pagado' => 'required|numeric|min:0',
            'cliente_ci' => 'nullable|max:12|exists:clientes,ci',
        ]);
        $pago->update($validated);
        return response()->json($pago);
    }

    public function destroyApi(int $id): JsonResponse
    {
        $pago = Pago::findOrFail($id);
        $pago->delete();
        return response()->json(['message' => 'Pago eliminado correctamente.']);
    }
}
