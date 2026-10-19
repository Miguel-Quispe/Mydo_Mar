<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AsignarAdmin extends Command
{
    protected $signature = 'admin:asignar {email}';
    protected $description = 'Asigna el rol de administrador (admin=true) a un usuario por su email';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró ningún usuario con el email: {$email}");
            return 1;
        }

        $user->admin = true;
        $user->save();

        $this->info("El usuario '{$user->nombre}' ({$user->email}) ahora es Administrador.");
        return 0;
    }
}
