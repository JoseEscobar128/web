<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos el ID del rol de Superadmin
        $superadminRole = Role::where('codigo', 'SUPERADMIN')->first();

        User::create([
            'name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'danielrefa23@gmail.com',
            'password' => Hash::make('DanMan_20'),
            'email_verified_at' => now(),
            'role_id' => $superadminRole->id,
            'esta_activo' => true
        ]);
    }
}