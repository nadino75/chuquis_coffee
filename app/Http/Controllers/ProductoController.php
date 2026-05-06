<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class ProductoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|borrar-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-producto', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $productos = Producto::with('categoria')->paginate(10);
        $categorias = Categoria::all();

        return view('producto.index', compact('productos', 'categorias'))
            ->with('i', ($request->input('page', 1) - 1) * $productos->perPage());
    }

    public function create(): View
    {
        $producto = new Producto();
        $categorias = Categoria::all();
        $permission = Permission::get();

        return view('producto.create', compact('producto', 'categorias', 'permission'));
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('productos', $imageName, 'public');
                $data['imagen'] = $imagePath;
            }

            if (!isset($data['stock_minimo']) || empty($data['stock_minimo'])) {
                $data['stock_minimo'] = 5;
            }

            Producto::create($data);

            return Redirect::route('productos.index')
                ->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        $userRoleId = Auth::user()?->roles->first()?->id ?? 0;
        $productoPermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $userRoleId)
            ->get();

        return view('producto.show', compact('producto', 'productoPermissions'));
    }

    public function edit(int $id): View
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $permission = Permission::get();
        $userRoleId = Auth::user()?->roles->first()?->id ?? 0;
        $productoPermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $userRoleId)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('producto.edit', compact('producto', 'categorias', 'permission', 'productoPermissions'));
    }

    public function update(ProductoRequest $request, int $id): RedirectResponse
    {
        try {
            $producto = Producto::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('imagen')) {
                if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                    Storage::disk('public')->delete($producto->imagen);
                }

                $image = $request->file('imagen');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('productos', $imageName, 'public');
                $data['imagen'] = $imagePath;
            }

            $producto->update($data);

            return Redirect::route('productos.index')
                ->with('success', 'Producto actualizado correctamente.');

        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $producto = Producto::findOrFail($id);

            if ($producto->ventas()->count() > 0) {
                return Redirect::route('productos.index')
                    ->with('error', 'No se puede eliminar el producto porque tiene ventas asociadas.');
            }

            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->delete();

            return Redirect::route('productos.index')
                ->with('success', 'Producto eliminado correctamente.');

        } catch (\Exception $e) {
            return Redirect::route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function actualizarStock(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'tipo' => 'required|in:entrada,salida,ajuste',
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
                ->with('success', "Stock actualizado. Anterior: {$stockAnterior}, Actual: {$producto->stock}");

        } catch (\Exception $e) {
            return Redirect::back()
                ->with('error', 'Error al actualizar stock: ' . $e->getMessage());
        }
    }

    // API Methods
    public function indexApi(): JsonResponse
    {
        $productos = Producto::with(['categoria', 'marca'])->paginate(10);
        return response()->json($productos);
    }

    public function showApi(int $id): JsonResponse
    {
        $producto = Producto::with(['categoria', 'marca'])->findOrFail($id);
        return response()->json($producto);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
        ]);
        $producto = Producto::create($validated);
        return response()->json($producto, 201);
    }

    public function updateApi(Request $request, int $id): JsonResponse
    {
        $producto = Producto::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
        ]);
        $producto->update($validated);
        return response()->json($producto);
    }

    public function destroyApi(int $id): JsonResponse
    {
        $producto = Producto::findOrFail($id);

        if ($producto->ventas()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el producto porque tiene ventas asociadas.',
            ], 422);
        }

        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }
}
