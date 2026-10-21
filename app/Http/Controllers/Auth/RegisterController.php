<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DimTiempo;
use App\Models\EmpresaRol;
use App\Models\EmpresaUser;
use App\Models\User;
use App\Models\UserCuenta;
use App\Services\DimTiempoService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
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
    | Maneja el registro de nuevos usuarios en el sistema, validaciones y
    | alta automática de empresas con sus 4 subroles operativos de fábrica.
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
        // 1. Maestro Carpintero
        if ($user->rol_id == 1) {
            return redirect()->route('maestro.dashboard')->with('success', '¡Bienvenido a tu panel de Maestro Carpintero!');
        }

        // 3. Empresa B2B / Gerente
        if ($user->rol_id == 3) {
            return redirect()->route('empresa.empleados.index')->with('success', '¡Empresa registrada exitosamente! Se han configurado los 4 subroles operativos de fábrica.');
        }

        // 2. Cliente / Visita (por defecto)
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
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidoPaterno' => ['required', 'string', 'max:255'],
            'apellidoMaterno' => ['nullable', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol_id' => ['required', 'in:1,2,3'],
        ];

        // Reglas condicionales si es registro de Empresa B2B (rol_id = 3)
        if (isset($data['rol_id']) && (int)$data['rol_id'] === 3) {
            $rules['razon_social'] = ['required', 'string', 'max:191'];
            $rules['nit'] = ['required', 'string', 'max:45', 'unique:empresa_users,nit_empresa_users'];
            $rules['telefono_whatsapp'] = ['nullable', 'string', 'max:30'];
        }

        $messages = [
            'razon_social.required' => 'La razón social o nombre de la empresa es obligatorio.',
            'nit.required' => 'El NIT o identificación fiscal de la empresa es obligatorio.',
            'nit.unique' => 'Este NIT ya se encuentra registrado por otra empresa.',
            'rol_id.in' => 'El tipo de cuenta seleccionado no es válido.',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Crear una nueva instancia de usuario y alta corporativa si aplica.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $rolId = (int) ($data['rol_id'] ?? 2);

            // Genera un prefijo para la clave según el rol elegido
            $prefijo = match ($rolId) {
                1 => 'MST-',
                3 => 'EMP-',
                default => 'CLI-',
            };

            $clave_rol = $prefijo . strtoupper(Str::random(4));
            while (User::where('clave_rol', $clave_rol)->exists()) {
                $clave_rol = $prefijo . strtoupper(Str::random(4));
            }

            // 1. Crear usuario principal
            $user = User::create([
                'nombre' => $data['nombre'],
                'apellidoPaterno' => $data['apellidoPaterno'],
                'apellidoMaterno' => !empty($data['apellidoMaterno']) ? $data['apellidoMaterno'] : '-',
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'rol_id' => $rolId,
                'clave_rol' => $clave_rol,
                'password' => Hash::make($data['password']),
            ]);

            // 2. Si el rol es Empresa B2B (rol_id == 3): Alta de empresa y sus 4 subroles de fábrica
            if ($rolId === 3) {
                $smartKey = DimTiempoService::obtenerOCrearSmartKey();

                // Asegurar que el rol 2 (Gerente Empresa) exista en users_roles
                DB::table('users_roles')->updateOrInsert(
                    ['id_rol_users' => 2],
                    ['nombre_rol_users' => 'Gerente Empresa']
                );

                // Crear la Empresa (Tenant)
                $empresa = EmpresaUser::create([
                    'id_dim_tiempo_creacion' => $smartKey,
                    'razon_social_empresa_users' => $data['razon_social'],
                    'nit_empresa_users' => $data['nit'],
                    'telefono_whatsapp_empresa_users' => !empty($data['telefono_whatsapp']) ? $data['telefono_whatsapp'] : $data['telefono'],
                    'activa_empresa_users' => true,
                ]);

                // Alta automática de los 4 subroles operativos internos de planta
                $subrolesConfiguracion = [
                    ['nombre' => 'Armador de Muebles', 'predeterminado' => true],
                    ['nombre' => 'Diseñador 3D / Modulador', 'predeterminado' => false],
                    ['nombre' => 'Supervisor de Calidad', 'predeterminado' => false],
                    ['nombre' => 'Transporte y Montaje', 'predeterminado' => false],
                ];

                $idSubrolDisenador = null;
                foreach ($subrolesConfiguracion as $sub) {
                    $subrolCreado = EmpresaRol::create([
                        'id_empresa_users' => $empresa->id_empresa_users,
                        'id_dim_tiempo_creacion' => $smartKey,
                        'nombre_rol_empresa' => $sub['nombre'],
                        'es_rol_predeterminado' => $sub['predeterminado'],
                    ]);

                    if ($sub['nombre'] === 'Diseñador 3D / Modulador') {
                        $idSubrolDisenador = $subrolCreado->id_empresa_rol;
                    }
                }

                // Generar username corporativo único para el gerente
                $baseUsername = Str::slug($data['nombre'], '_');
                if (empty($baseUsername)) {
                    $baseUsername = 'gerente';
                }
                $nombreUsuario = $baseUsername . '_' . rand(100, 999);
                while (UserCuenta::where('nombre_usuario_users', $nombreUsuario)->exists()) {
                    $nombreUsuario = $baseUsername . '_' . rand(1000, 9999);
                }

                // Crear cuenta vinculada en users_cuentas para control gerencial y middleware B2B
                UserCuenta::create([
                    'id_empresa_users' => $empresa->id_empresa_users,
                    'id_rol_users' => 2, // 2 = Gerente Empresa en users_roles
                    'id_empresa_rol' => $idSubrolDisenador,
                    'id_dim_tiempo_registro' => $smartKey,
                    'nombre_usuario_users' => $nombreUsuario,
                    'email_usuario_users' => $data['email'],
                    'password_hash_users' => Hash::make($data['password']),
                    'cuenta_generada_por_empresa' => false,
                ]);
            }

            return $user;
        });
    }
}
