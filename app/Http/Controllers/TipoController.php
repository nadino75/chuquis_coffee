<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TipoRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class TipoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-tipo|crear-tipo|editar-tipo|borrar-tipo', ['only' => ['index']]);
        $this->middleware('permission:crear-tipo', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-tipo', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-tipo', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $tipos = Tipo::paginate();

        return view('tipo.index', compact('tipos'))
            ->with('i', ($request->input('page', 1) - 1) * $tipos->perPage());
    }

    public function create(): View
    {
        $tipo = new Tipo();
        $permission = Permission::get();

        return view('tipo.create', compact('tipo', 'permission'));
    }

    public function store(TipoRequest $request): RedirectResponse
    {
        Tipo::create($request->validated());

        return Redirect::route('tipos.index')
            ->with('success', 'Tipo creado correctamente.');
    }

    public function show(int $id): View
    {
        $tipo = Tipo::findOrFail($id);

        return view('tipo.show', compact('tipo'));
    }

    public function edit(int $id): View
    {
        $tipo = Tipo::findOrFail($id);
        $userRoleId = Auth::user()?->roles->first()?->id ?? 0;
        $tipoPermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $userRoleId)
            ->get();

        return view('tipo.edit', compact('tipo', 'tipoPermissions'));
    }

    public function update(TipoRequest $request, int $id): RedirectResponse
    {
        $tipo = Tipo::findOrFail($id);
        $tipo->update($request->validated());

        return Redirect::route('tipos.index')
            ->with('success', 'Tipo actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Tipo::findOrFail($id)->delete();

        return Redirect::route('tipos.index')
            ->with('success', 'Tipo eliminado correctamente.');
    }

    // API Methods
    public function indexApi(): JsonResponse
    {
        $tipos = Tipo::paginate(10);
        return response()->json($tipos);
    }

    public function showApi(int $id): JsonResponse
    {
        $tipo = Tipo::findOrFail($id);
        return response()->json($tipo);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
        ]);
        $tipo = Tipo::create($validated);
        return response()->json($tipo, 201);
    }

    public function updateApi(Request $request, int $id): JsonResponse
    {
        $tipo = Tipo::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
        ]);
        $tipo->update($validated);
        return response()->json($tipo);
    }

    public function destroyApi(int $id): JsonResponse
    {
        $tipo = Tipo::findOrFail($id);
        $tipo->delete();
        return response()->json(['message' => 'Tipo eliminado correctamente.']);
    }
}
