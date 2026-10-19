<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Dónde redirigir a los usuarios tras el registro.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * El usuario ha sido registrado exitosamente.
     */
    protected function registered($request, $user)
    {
        // Redirección según el rol_id del usuario
        if ($user->rol_id == 1) {
            return redirect()->route('maestro.dashboard')->with('success', true);
        }

        // Por defecto (rol_id = 2) manda al catálogo público
        return redirect()->route('catalogo.index')->with('success', true);
    }

    /**
     * Obtener un validador para las peticiones de registro entrantes.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidoPaterno' => ['required', 'string', 'max:255'],
            'apellidoMaterno' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol_id' => ['required', 'integer'], // 1 = Maestro Carpintero, 2 = Cliente / Visita
        ]);
    }

    /**
     * Crear una nueva instancia de usuario tras una validación correcta.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Genera un prefijo para la clave según el rol elegido
        $prefijo = ($data['rol_id'] == 1) ? 'MST-' : 'CLI-';
        $clave_rol = $prefijo . strtoupper(Str::random(4));

        return User::create([
            'nombre' => $data['nombre'],
            'apellidoPaterno' => $data['apellidoPaterno'],
            'apellidoMaterno' => $data['apellidoMaterno'],
            'telefono' => $data['telefono'],
            'email' => $data['email'],
            'rol_id' => $data['rol_id'],
            'clave_rol' => $clave_rol,
            'password' => Hash::make($data['password']),
        ]);
    }
}