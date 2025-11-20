<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TipoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use DB;

class TipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:ver-tipo|crear-tipo|editar-tipo|borrar-tipo', ['only' => ['index']]);
        $this->middleware('permission:crear-tipo', ['only' => ['create','store']]);
        $this->middleware('permission:editar-tipo', ['only' => ['edit','update']]);
        $this->middleware('permission:borrar-tipo', ['only' => ['destroy']]);
    }
    public function index(Request $request): View
    {
        $tipos = Tipo::paginate();

        return view('tipo.index', compact('tipos'))
            ->with('i', ($request->input('page', 1) - 1) * $tipos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tipo = new Tipo();
        $permission = Permission::get();

        return view('tipo.create', compact('tipo', 'permission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TipoRequest $request): RedirectResponse
    {
        Tipo::create($request->validated());

        return Redirect::route('tipos.index')
            ->with('success', 'Tipo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $tipo = Tipo::find($id);

        return view('tipo.show', compact('tipo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $tipo = Tipo::find($id);
        $tipoPermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('tipo.edit', compact('tipo', 'tipoPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TipoRequest $request, $id): RedirectResponse
    {
        $tipo = Tipo::find($id);
        $tipo->update($request->validated());

        return Redirect::route('tipos.index')
            ->with('success', 'Tipo updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Tipo::find($id)->delete();

        return Redirect::route('tipos.index')
            ->with('success', 'Tipo deleted successfully');
    }
}
