<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|borrar-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create','store']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit','update']]);
        $this->middleware('permission:borrar-producto', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        // Traer productos con su categoria
        $productos = Producto::with('categoria')->paginate(10);
        $categorias = Categoria::all();

        return view('producto.index', compact('productos', 'categorias'))
            ->with('i', ($request->input('page', 1) - 1) * $productos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $producto = new Producto();
        $categorias = Categoria::all();
        $permission = Permission::get();

        return view('producto.create', compact('producto', 'categorias', 'permission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Procesar imagen si se subió
            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Guardar imagen en storage
                $imagePath = $image->storeAs('productos', $imageName, 'public');
                $data['imagen'] = $imagePath;

                // Opcional: Crear thumbnail
                $thumbnail = Image::make($image->getRealPath());
                $thumbnail->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $thumbnailPath = 'productos/thumb_' . $imageName;
                Storage::disk('public')->put($thumbnailPath, $thumbnail->stream());
            }

            // Establecer stock_minimo por defecto si no se proporciona
            if (!isset($data['stock_minimo']) || empty($data['stock_minimo'])) {
                $data['stock_minimo'] = 5;
            }

            // Crear producto
            Producto::create($data);

            return Redirect::route('productos.index')
                ->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        
        // Obtener permisos del usuario actual, no del producto
        $productoPermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", auth()->user()->roles->first()->id ?? 0)
            ->get();

        return view('producto.show', compact('producto', 'productoPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $permission = Permission::get();
        
        // Obtener permisos del usuario actual
        $productoPermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", auth()->user()->roles->first()->id ?? 0)
            ->pluck('role_has_permissions.permission_id')
            ->all();

        return view('producto.edit', compact('producto', 'categorias', 'permission', 'productoPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, $id): RedirectResponse
    {
        try {
            $producto = Producto::findOrFail($id);
            $data = $request->validated();

            // Procesar imagen si se subió una nueva
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                    Storage::disk('public')->delete($producto->imagen);
                    
                    // Eliminar thumbnail si existe
                    $thumbnailPath = 'productos/thumb_' . basename($producto->imagen);
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->delete($thumbnailPath);
                    }
                }

                $image = $request->file('imagen');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Guardar nueva imagen
                $imagePath = $image->storeAs('productos', $imageName, 'public');
                $data['imagen'] = $imagePath;

                // Crear thumbnail
                $thumbnail = Image::make($image->getRealPath());
                $thumbnail->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $thumbnailPath = 'productos/thumb_' . $imageName;
                Storage::disk('public')->put($thumbnailPath, $thumbnail->stream());
            }

            // Actualizar producto
            $producto->update($data);

            return Redirect::route('productos.index')
                ->with('success', 'Producto actualizado correctamente.');

        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $producto = Producto::findOrFail($id);

            // Verificar si el producto tiene ventas asociadas
            if ($producto->ventas()->count() > 0) {
                return Redirect::route('productos.index')
                    ->with('error', 'No se puede eliminar el producto porque tiene ventas asociadas.');
            }

            // Eliminar imagen si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
                
                // Eliminar thumbnail si existe
                $thumbnailPath = 'productos/thumb_' . basename($producto->imagen);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }

            $producto->delete();

            return Redirect::route('productos.index')
                ->with('success', 'Producto eliminado correctamente.');

        } catch (\Exception $e) {
            return Redirect::route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Método adicional para actualizar stock
     */
    public function actualizarStock(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'tipo' => 'required|in:entrada,salida,ajuste'
        ]);

        try {
            $producto = Producto::findOrFail($id);
            $stockAnterior = $producto->stock;
            $nuevoStock = $request->stock;

            switch ($request->tipo) {
                case 'entrada':
                    $producto->aumentarStock($nuevoStock);
                    break;
                case 'salida':
                    $producto->reducirStock($nuevoStock);
                    break;
                case 'ajuste':
                    $producto->stock = $nuevoStock;
                    $producto->save();
                    break;
            }

            return Redirect::back()
                ->with('success', "Stock actualizado correctamente. Anterior: {$stockAnterior}, Actual: {$producto->stock}");

        } catch (\Exception $e) {
            return Redirect::back()
                ->with('error', 'Error al actualizar stock: ' . $e->getMessage());
        }
    }
}