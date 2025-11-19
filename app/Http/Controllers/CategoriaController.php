<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Tipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $categorias = Categoria::with(['tipo', 'categoria_padre'])->paginate(10);
        $tipos = Tipo::all(); // Para el select de tipo de producto
        $categoriasPadre = Categoria::all(); // Para el select de categoría padre

        return view('categoria.index', compact('categorias', 'tipos', 'categoriasPadre'))
            ->with('i', ($request->input('page', 1) - 1) * $categorias->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categoria = new Categoria();
        $tipos = Tipo::all();
        $categoriasPadre = Categoria::all();

        return view('categoria.create', compact('categoria', 'tipos', 'categoriasPadre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());

        return Redirect::route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $categoria = Categoria::with(['tipo', 'categoria_padre'])->findOrFail($id);

        return view('categoria.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $categoria = Categoria::findOrFail($id);
        $tipos = Tipo::all();
        $categoriasPadre = Categoria::all();

        return view('categoria.edit', compact('categoria', 'tipos', 'categoriasPadre'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());

        return Redirect::route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return Redirect::route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
