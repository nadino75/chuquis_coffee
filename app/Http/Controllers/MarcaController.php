<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MarcaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MarcaController extends Controller
{
    public function index(Request $request): View
    {
        $marcas = Marca::paginate();

        return view('marca.index', compact('marcas'))
            ->with('i', ($request->input('page', 1) - 1) * $marcas->perPage());
    }

    public function create(): View
    {
        $marca = new Marca();

        return view('marca.create', compact('marca'));
    }

    public function store(MarcaRequest $request): RedirectResponse
    {
        Marca::create($request->validated());

        return Redirect::route('marcas.index')
            ->with('success', 'Marca creada correctamente.');
    }

    public function show(int $id): View
    {
        $marca = Marca::findOrFail($id);

        return view('marca.show', compact('marca'));
    }

    public function edit(int $id): View
    {
        $marca = Marca::findOrFail($id);

        return view('marca.edit', compact('marca'));
    }

    public function update(MarcaRequest $request, Marca $marca): RedirectResponse
    {
        $marca->update($request->validated());

        return Redirect::route('marcas.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Marca::findOrFail($id)->delete();

        return Redirect::route('marcas.index')
            ->with('success', 'Marca eliminada correctamente.');
    }

    // API Methods
    public function indexApi(): JsonResponse
    {
        $marcas = Marca::paginate(10);
        return response()->json($marcas);
    }

    public function showApi(int $id): JsonResponse
    {
        $marca = Marca::findOrFail($id);
        return response()->json($marca);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
        ]);
        $marca = Marca::create($validated);
        return response()->json($marca, 201);
    }

    public function updateApi(Request $request, int $id): JsonResponse
    {
        $marca = Marca::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
        ]);
        $marca->update($validated);
        return response()->json($marca);
    }

    public function destroyApi(int $id): JsonResponse
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();
        return response()->json(['message' => 'Marca eliminada correctamente.']);
    }
}
