<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
/* use Hash;
use Illuminate\Support\Arr; */
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    //
    /**
     * @return Illuminate\Http\Response
     */

    function __construct()
    {
        //definir las 4 opciones que hay en el rol del CRUD
        $this->middleware('permission:ver-rol|crear-rol|editar-rol|borrar-rol',['only'=>['index']]);
        //middleware de asignación de permisos
        $this->middleware('permission:crear-rol',['only'=>['create','store']]);
        $this->middleware('permission:editar-rol',['only'=>['edit','update']]);
        $this->middleware('permission:borrar-rol',['only'=>['destroy']]);
    }

    /** 
     * @return Illuminate\Https\Response
     */

    public function index(Request $request): View{
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view ('roles.index', compact('roles'))
            ->with('i', ($request->input('page',1)-1)*5);
    }

    /**
     * @return Illuminate\Http\Response
     */

    public function create(): View{
        $permission = Permission::get();
        return view('roles.create', compact('permission'));
    }

    /**
     * @return Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
    
    */

    public function store(Request $request): RedirectResponse{
        //hay que asignar siosi a un rol
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required'
        ]);

        $permissionsID = array_map(
            function ($value) {
                return (int)$value;
            },
            $request->input('permission')
        );
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($permissionsID);

        return redirect()->route('roles.index')
            ->with('success', ' Rol creado correctamente');
    }

    /**
     * @param int $id
     * @return Illuminate\Http\Response
     */

    public function show($id): View{
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('roles.show', compact('role','rolePermissions'));
    }

    public function edit($id): View{
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('roles.edit', compact('role','permission','rolePermissions'));
    }

    public function update(Request $request, $id): RedirectResponse{
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required'
        ]);
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $permissionsID = array_map(
            function($value) {
                return (int)$value;
            },
            $request->input('permission')
        );
        $role->syncPermissions($permissionsID);
        return redirect()->route('roles.index')
            ->with('success', ' Rol actualizado correctamente');
    }

    public function destroy($id): RedirectResponse{
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente');
    }

} 