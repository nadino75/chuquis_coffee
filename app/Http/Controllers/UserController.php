<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Illuminate\Support\Arr; 
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    //
    /**
     * @return Illuminate\Http\Response
     */
    public function index(Request $request): View{
        $data = User::latest()->paginate(5);
        return view('users.index',compact('data'))
        ->with('i', ($request->input('page', 1) -1) *5);
    }
    /** 
    * @return Illuminate\Http\Response
    */

    public function create(): View{
        $roles = Role::pluck('name', 'name')->all();    //saca de la base de datos a los usuarios,
        //  y en el segundo name compara con la base de datos

        return view('users.create', compact('roles'));    
    }

    /** 
    * @return Illuminate\Http\Request $request
    * @return Illuminate\Http\Response
    */

    public function store(Request $request): RedirectResponse{
        $this->validate($request,
        [ 
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|same:confirm-password',
        'roles' => 'required']);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /** 
    * @param int $id
    * @return Illuminate\Http\Response
    */

    public function show($id) : View{
        $user = User::find ($id);
        return view('users.show', compact('user'));
    }

    /** 
    * @param int $id
    * @return Illuminate\Http\Response
    */

    public function edit($id) : View{
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id) : RedirectResponse{
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'. $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input, array('password'));
        }
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /** 
    * @param int $id
    * @return Illuminate\Http\Response
    */

    public function destroy($id): RedirectResponse
    {
        if ((int) $id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    // API Methods
    public function indexApi(Request $request): JsonResponse
    {
        $users = User::with('roles')->latest()->paginate(10);
        return response()->json($users);
    }

    public function showApi($id): JsonResponse
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'nullable|string',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }
        return response()->json($user, 201);
    }

    public function updateApi(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'nullable|string',
        ]);
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);
        if (!empty($validated['role'])) {
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user->assignRole($validated['role']);
        }
        return response()->json($user->load('roles'));
    }

    public function destroyApi($id): JsonResponse
    {
        if ((int) $id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario.'], 403);
        }
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
