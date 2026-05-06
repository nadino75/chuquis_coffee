<?php

namespace App\Http\Controllers;

use App\Models\ProveedoresProducto;
use App\Models\Proveedore;
use App\Models\Producto;
use App\Models\Marca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProveedoresProductoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProveedoresProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $proveedoresProductos = ProveedoresProducto::with(['proveedore', 'producto', 'marca'])->paginate();

        $proveedores = Proveedore::all();
        $productos = Producto::all();
        $marcas = Marca::all();

        return view('proveedores-producto.index', compact(
            'proveedoresProductos',
            'proveedores',
            'productos',
            'marcas'
        ))->with('i', ($request->input('page', 1) - 1) * $proveedoresProductos->perPage());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProveedoresProductoRequest $request): RedirectResponse
    {
        ProveedoresProducto::create($request->validated());

        return Redirect::route('proveedores_productos.index')
            ->with('success', 'Registro creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProveedoresProducto $proveedoresProducto): View
    {
        return view('proveedores-producto.show', compact('proveedoresProducto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProveedoresProducto $proveedoresProducto): View
    {
        $proveedores = Proveedore::all();
        $productos = Producto::all();
        $marcas = Marca::all();

        return view('proveedores-producto.edit', compact(
            'proveedoresProducto',
            'proveedores',
            'productos',
            'marcas'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProveedoresProductoRequest $request, ProveedoresProducto $proveedoresProducto): RedirectResponse
    {
        $proveedoresProducto->update($request->validated());

        return Redirect::route('proveedores_productos.index')
            ->with('success', 'Registro actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        ProveedoresProducto::findOrFail($id)->delete();

        return Redirect::route('proveedores_productos.index')
            ->with('success', 'Registro eliminado exitosamente.');
    }

    // API Methods
    public function indexApi(Request $request): JsonResponse
    {
        $proveedoresProductos = ProveedoresProducto::with(['proveedore', 'producto', 'marca'])->paginate(10);
        return response()->json($proveedoresProductos);
    }

    public function showApi($id): JsonResponse
    {
        $proveedoresProducto = ProveedoresProducto::with(['proveedore', 'producto', 'marca'])->findOrFail($id);
        return response()->json($proveedoresProducto);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'proveedore_id' => 'required|exists:proveedores,id',
            'producto_id' => 'required|exists:productos,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'precio' => 'nullable|numeric|min:0',
        ]);
        $proveedoresProducto = ProveedoresProducto::create($validated);
        return response()->json($proveedoresProducto, 201);
    }

    public function updateApi(Request $request, $id): JsonResponse
    {
        $proveedoresProducto = ProveedoresProducto::findOrFail($id);
        $validated = $request->validate([
            'proveedore_id' => 'required|exists:proveedores,id',
            'producto_id' => 'required|exists:productos,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'precio' => 'nullable|numeric|min:0',
        ]);
        $proveedoresProducto->update($validated);
        return response()->json($proveedoresProducto);
    }

    public function destroyApi($id): JsonResponse
    {
        $proveedoresProducto = ProveedoresProducto::findOrFail($id);
        $proveedoresProducto->delete();
        return response()->json(['message' => 'Registro eliminado exitosamente']);
    }
}
