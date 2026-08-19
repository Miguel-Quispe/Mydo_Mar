<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mueble;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('perfil.index', compact('user'));
    }

    /**
     * Panel de Control Total del Administrador
     */
    public function adminDashboard()
    {
        $user = auth()->user();
        
        // Métricas
        $totalUsuarios = User::count();
        $totalAdmins   = User::where('admin', true)->count();
        $totalMaestros = User::where('admin', false)
                             ->where(function($q) {
                                 $q->where('clave_rol', 'LIKE', 'MAE%')
                                   ->orWhere('rol_id', 1);
                             })->count();
        $totalClientes = User::where('admin', false)
                             ->where('rol_id', 2)
                             ->count();
        $totalMuebles  = Mueble::count();

        // Listados para control del Admin (Paginados)
        $usuarios = User::orderBy('id', 'desc')->paginate(10, ['*'], 'users_page');
        $muebles  = Mueble::with('user')->orderBy('id', 'desc')->paginate(10, ['*'], 'muebles_page');

        return view('admin.perfil', compact(
            'user', 
            'totalUsuarios', 
            'totalAdmins', 
            'totalMaestros', 
            'totalClientes', 
            'totalMuebles',
            'usuarios',
            'muebles'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'telefono' => 'required|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->nombre   = $request->nombre;
        $user->email    = $request->email;
        $user->telefono = $request->telefono;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('exito', 'Perfil actualizado correctamente.');
    }

    /**
     * Cambiar estado de Administrador a un usuario
     */
    public function toggleAdmin($id)
    {
        $usuario = User::findOrFail($id);
        
        // Evitar que el admin se quite sus propios permisos por accidente
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes quitarte los permisos de administrador a ti mismo.');
        }

        $usuario->admin = !$usuario->admin;
        $usuario->save();

        return back()->with('exito', 'Estado de administrador actualizado para ' . $usuario->nombre);
    }

    /**
     * Eliminar usuario desde el panel admin
     */
    public function destroyUser($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $usuario->delete();
        return back()->with('exito', 'Usuario eliminado correctamente.');
    }

    /**
     * Eliminar mueble desde el panel admin
     */
    public function destroyMueble($id)
    {
        $mueble = Mueble::findOrFail($id);
        $mueble->delete();

        return back()->with('exito', 'Mueble eliminado por el administrador.');
    }
}
