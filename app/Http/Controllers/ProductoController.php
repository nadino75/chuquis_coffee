<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Traer productos con su categoria
        $productos = Producto::with('categoria')->paginate(10);
        $categorias = Categoria::all(); // Necesario para los modales

        return view('producto.index', compact('productos', 'categorias'))
            ->with('i', ($request->input('page', 1) - 1) * $productos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $producto = new Producto();
        $categorias = Categoria::all(); // Pasar categorias al formulario

        return view('producto.create', compact('producto', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return Redirect::route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $producto = Producto::with('categoria')->findOrFail($id);

        return view('producto.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all(); // Pasar categorias al formulario

        return view('producto.edit', compact('producto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return Redirect::route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return Redirect::route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
