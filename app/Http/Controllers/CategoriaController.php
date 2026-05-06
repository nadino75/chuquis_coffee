<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Tipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use DB;

class CategoriaController extends Controller
{
        function __construct()
    {
        $this->middleware('permission:ver-categoria|crear-categoria|editar-categoria|borrar-categoria', ['only' => ['index']]);
        $this->middleware('permission:crear-categoria', ['only' => ['create','store']]);
        $this->middleware('permission:editar-categoria', ['only' => ['edit','update']]);
        $this->middleware('permission:borrar-categoria', ['only' => ['destroy']]);
    }
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
        $permission = Permission::get();

        return view('categoria.create', compact('categoria', 'tipos', 'categoriasPadre', 'permission'));
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
        $userRoleId = auth()->user()->roles->first()->id ?? 0;
        $categoriaPermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $userRoleId)
            ->get();
        return view('categoria.show', compact('categoria', 'categoriaPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $categoria = Categoria::findOrFail($id);
        $tipos = Tipo::all();
        $categoriasPadre = Categoria::all();
        $permission = Permission::get();
        $userRoleId = auth()->user()->roles->first()->id ?? 0;
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $userRoleId)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('categoria.edit', compact('categoria', 'tipos', 'categoriasPadre', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaRequest $request, $id): RedirectResponse
    {
        $categoria = Categoria::findOrFail($id);
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

    // API Methods
    public function indexApi(Request $request): JsonResponse
    {
        $categorias = Categoria::with(['tipo', 'categoria_padre'])->paginate(10);
        return response()->json($categorias);
    }

    public function showApi($id): JsonResponse
    {
        $categoria = Categoria::with(['tipo', 'categoria_padre'])->findOrFail($id);
        return response()->json($categoria);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable',
            'tipo_id' => 'nullable|exists:tipos,id',
            'categoria_padre_id' => 'nullable|exists:categorias,id',
        ]);
        $categoria = Categoria::create($validated);
        return response()->json($categoria, 201);
    }

    public function updateApi(Request $request, $id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable',
            'tipo_id' => 'nullable|exists:tipos,id',
            'categoria_padre_id' => 'nullable|exists:categorias,id',
        ]);
        $categoria->update($validated);
        return response()->json($categoria);
    }

    public function destroyApi($id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada exitosamente']);
    }
}
