<?php

namespace App\Http\Controllers;

use App\Models\ProveedoresProducto;
use App\Models\Proveedore;
use App\Models\Producto;
use App\Models\Marca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // Cargar registros con relaciones
        $proveedoresProductos = ProveedoresProducto::with(['proveedore', 'producto', 'marca'])->paginate();

        // Datos para los selects de los modales
        $proveedores = Proveedore::all();
        $productos = Producto::all();
        $marcas = Marca::all();
        $unidades = ['unidad', 'kilogramos', 'litros', 'caja', 'bolsa'];

        return view('proveedores-producto.index', compact(
            'proveedoresProductos',
            'proveedores',
            'productos',
            'marcas',
            'unidades'
        ))->with('i', ($request->input('page', 1) - 1) * $proveedoresProductos->perPage());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProveedoresProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['unidad'] = $request->input('unidad', 'unidad'); // Valor por defecto
        ProveedoresProducto::create($data);

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
        $unidades = ['unidad', 'kilogramos', 'litros', 'caja', 'bolsa'];

        return view('proveedores-producto.edit', compact(
            'proveedoresProducto',
            'proveedores',
            'productos',
            'marcas',
            'unidades'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProveedoresProductoRequest $request, ProveedoresProducto $proveedoresProducto): RedirectResponse
    {
        $data = $request->validated();
        $data['unidad'] = $request->input('unidad', 'unidad'); // Valor por defecto
        $proveedoresProducto->update($data);

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
}
