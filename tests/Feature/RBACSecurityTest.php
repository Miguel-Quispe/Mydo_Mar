<?php

namespace Tests\Feature;

use App\Models\EmpresaRol;
use App\Models\EmpresaUser;
use App\Models\User;
use App\Models\UserCuenta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RBACSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'b2b_gerente'])
            ->get('/_test/gerente', fn () => response('OK_GERENTE', 200));

        \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'armador_taller'])
            ->get('/_test/taller', fn () => response('OK_TALLER', 200));

        \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'disenador_3d'])
            ->get('/_test/disenador', fn () => response('OK_DISENADOR', 200));
    }

    /**
     * Verifica que usuarios invitados (no autenticados) sean redirigidos al login.
     */
    public function test_invitados_son_redirigidos_al_login_en_rutas_protegidas(): void
    {
        $responseEmpresa = $this->get('/empresa/empleados');
        $responseEmpresa->assertRedirect('/login');

        $responseTaller = $this->get('/taller');
        $responseTaller->assertRedirect('/login');

        $responseDiseno = $this->get('/muebles/crear');
        $responseDiseno->assertRedirect('/login');
    }

    /**
     * Verifica que un cliente o usuario sin privilegios B2B reciba 403 en /empresa/*.
     */
    public function test_usuario_sin_rol_gerente_es_bloqueado_con_403_en_empresa(): void
    {
        $usuarioComun = User::factory()->make([
            'email' => 'cliente_prueba@mydomar.com',
            'rol_id' => 2, // Cliente común
            'admin' => false,
        ]);

        $response = $this->actingAs($usuarioComun)->get('/empresa/empleados');
        $response->assertStatus(403);

        $responseTest = $this->actingAs($usuarioComun)->get('/_test/gerente');
        $responseTest->assertStatus(403);
    }

    /**
     * Verifica que un Gerente de Empresa B2B o Admin acceda exitosamente a /empresa/*.
     */
    public function test_gerente_b2b_o_admin_accede_exitosamente_a_empresa(): void
    {
        $admin = User::factory()->make([
            'email' => 'admin_test@mydomar.com',
            'rol_id' => 0,
            'admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/_test/gerente');
        $response->assertStatus(200);
        $response->assertSee('OK_GERENTE');
    }

    /**
     * Verifica que un usuario común reciba 403 en el terminal de taller /taller.
     */
    public function test_usuario_no_autorizado_es_bloqueado_en_taller(): void
    {
        $usuarioCliente = User::factory()->make([
            'email' => 'cliente_taller@mydomar.com',
            'rol_id' => 2,
            'admin' => false,
        ]);

        $response = $this->actingAs($usuarioCliente)->get('/taller');
        $response->assertStatus(403);

        $responseTest = $this->actingAs($usuarioCliente)->get('/_test/taller');
        $responseTest->assertStatus(403);
    }

    /**
     * Verifica que operarios autorizados (Maestros / Admins) accedan a taller y diseñador.
     */
    public function test_personal_autorizado_accede_a_taller_y_disenador(): void
    {
        $maestro = User::factory()->make([
            'email' => 'maestro_test@mydomar.com',
            'rol_id' => 1, // Maestro Carpintero
            'admin' => false,
        ]);

        $responseTaller = $this->actingAs($maestro)->get('/_test/taller');
        $responseTaller->assertStatus(200);
        $responseTaller->assertSee('OK_TALLER');

        $responseDiseno = $this->actingAs($maestro)->get('/_test/disenador');
        $responseDiseno->assertStatus(200);
        $responseDiseno->assertSee('OK_DISENADOR');
    }
}
