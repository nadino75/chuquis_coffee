<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
/* use Hash;
use Illuminate\Support\Arr; */



class ClienteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-cliente|crear-cliente|editar-cliente|borrar-cliente', ['only' => ['index']]);
        $this->middleware('permission:crear-cliente', ['only' => ['create','store']]);
        $this->middleware('permission:editar-cliente', ['only' => ['edit','update']]);
        $this->middleware('permission:borrar-cliente', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->paginate(10);
        return view('cliente.index', compact('clientes'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Validación más flexible para testing
        $request->validate([
            'ci' => 'required|unique:clientes,ci|max:20',
            'NIT' => 'nullable|max:13',
            'nombres' => 'required|max:100',
            'apellido_paterno' => 'nullable|max:100',
            'apellido_materno' => 'nullable|max:100',
            'sexo' => 'nullable|in:masculino,femenino',
            'telefono' => 'nullable|max:15',
            'celular' => 'required|max:15',
            'correo' => 'nullable|email|max:100',
        ]);

        try {
            Cliente::create($request->all());
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente creado correctamente.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear cliente: ' . $e->getMessage());
        }
    }

    public function show($id): View
    {
        $cliente = Cliente::find($id);
        $clientePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('clientes.show', compact('cliente','clientePermissions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'ci' => 'required|max:20|unique:clientes,ci,' . $cliente->ci . ',ci',
            'NIT' => 'nullable|max:13',
            'nombres' => 'required|max:100',
            'apellido_paterno' => 'nullable|max:100',
            'apellido_materno' => 'nullable|max:100',
            'sexo' => 'nullable|in:masculino,femenino',
            'telefono' => 'nullable|max:15',
            'celular' => 'required|max:15',
            'correo' => 'nullable|email|max:100',
        ]);

        try {
            $cliente = Cliente::find($id); //agregado para obtener el cliente
            $cliente->update($request->all());
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado correctamente.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar cliente: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $cliente = Cliente::find($id);
            $cliente->delete();
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado correctamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'Error al eliminar cliente: ' . $e->getMessage());
        }
    }
}