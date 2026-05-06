<?php

namespace App\Http\Controllers;

use App\Models\PagosMixto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PagosMixtoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PagosMixtoController extends Controller
{
    public function index(Request $request): View
    {
        $pagosMixtos = PagosMixto::paginate();

        return view('pagos-mixto.index', compact('pagosMixtos'))
            ->with('i', ($request->input('page', 1) - 1) * $pagosMixtos->perPage());
    }

    public function create(): View
    {
        $pagosMixto = new PagosMixto();

        return view('pagos-mixto.create', compact('pagosMixto'));
    }

    public function store(PagosMixtoRequest $request): RedirectResponse
    {
        PagosMixto::create($request->validated());

        return Redirect::route('pagos-mixtos.index')
            ->with('success', 'Pago mixto creado correctamente.');
    }

    public function show(int $id): View
    {
        $pagosMixto = PagosMixto::findOrFail($id);

        return view('pagos-mixto.show', compact('pagosMixto'));
    }

    public function edit(int $id): View
    {
        $pagosMixto = PagosMixto::findOrFail($id);

        return view('pagos-mixto.edit', compact('pagosMixto'));
    }

    public function update(PagosMixtoRequest $request, PagosMixto $pagosMixto): RedirectResponse
    {
        $pagosMixto->update($request->validated());

        return Redirect::route('pagos-mixtos.index')
            ->with('success', 'Pago mixto actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        PagosMixto::findOrFail($id)->delete();

        return Redirect::route('pagos-mixtos.index')
            ->with('success', 'Pago mixto eliminado correctamente.');
    }

    // API Methods
    public function indexApi(): JsonResponse
    {
        $pagosMixtos = PagosMixto::paginate(10);
        return response()->json($pagosMixtos);
    }

    public function showApi(int $id): JsonResponse
    {
        $pagosMixto = PagosMixto::findOrFail($id);
        return response()->json($pagosMixto);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recibo' => 'nullable|max:50',
            'fecha' => 'nullable|date',
            'tipo_pago' => 'nullable|in:efectivo,tarjeta,transferencia,qr',
            'monto' => 'required|numeric|min:0',
            'pago_id' => 'nullable|exists:pagos,id',
        ]);
        $pagosMixto = PagosMixto::create($validated);
        return response()->json($pagosMixto, 201);
    }

    public function updateApi(Request $request, int $id): JsonResponse
    {
        $pagosMixto = PagosMixto::findOrFail($id);
        $validated = $request->validate([
            'recibo' => 'nullable|max:50',
            'fecha' => 'nullable|date',
            'tipo_pago' => 'nullable|in:efectivo,tarjeta,transferencia,qr',
            'monto' => 'required|numeric|min:0',
            'pago_id' => 'nullable|exists:pagos,id',
        ]);
        $pagosMixto->update($validated);
        return response()->json($pagosMixto);
    }

    public function destroyApi(int $id): JsonResponse
    {
        $pagosMixto = PagosMixto::findOrFail($id);
        $pagosMixto->delete();
        return response()->json(['message' => 'Pago mixto eliminado correctamente.']);
    }
}
