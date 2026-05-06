<?php

namespace App\Http\Controllers;

use App\Models\Proveedore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProveedoreRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProveedoreController extends Controller
{
    public function index(Request $request): View
    {
        $proveedores = Proveedore::paginate();

        return view('proveedore.index', compact('proveedores'))
            ->with('i', ($request->input('page', 1) - 1) * $proveedores->perPage());
    }

    public function create(): View
    {
        $proveedore = new Proveedore();

        return view('proveedore.create', compact('proveedore'));
    }

    public function store(ProveedoreRequest $request): RedirectResponse
    {
        Proveedore::create($request->validated());

        return Redirect::route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function show(int $id): View
    {
        $proveedore = Proveedore::findOrFail($id);

        return view('proveedore.show', compact('proveedore'));
    }

    public function edit(int $id): View
    {
        $proveedore = Proveedore::findOrFail($id);

        return view('proveedore.edit', compact('proveedore'));
    }

    public function update(ProveedoreRequest $request, Proveedore $proveedore): RedirectResponse
    {
        $proveedore->update($request->validated());

        return Redirect::route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Proveedore::findOrFail($id)->delete();

        return Redirect::route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }

    // API Methods
    public function indexApi(): JsonResponse
    {
        $proveedores = Proveedore::paginate(10);
        return response()->json($proveedores);
    }

    public function showApi(int $id): JsonResponse
    {
        $proveedore = Proveedore::findOrFail($id);
        return response()->json($proveedore);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'contacto' => 'nullable|max:100',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'ruc' => 'nullable|max:20',
        ]);
        $proveedore = Proveedore::create($validated);
        return response()->json($proveedore, 201);
    }

    public function updateApi(Request $request, int $id): JsonResponse
    {
        $proveedore = Proveedore::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'contacto' => 'nullable|max:100',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'ruc' => 'nullable|max:20',
        ]);
        $proveedore->update($validated);
        return response()->json($proveedore);
    }

    public function destroyApi(int $id): JsonResponse
    {
        $proveedore = Proveedore::findOrFail($id);
        $proveedore->delete();
        return response()->json(['message' => 'Proveedor eliminado correctamente.']);
    }
}
