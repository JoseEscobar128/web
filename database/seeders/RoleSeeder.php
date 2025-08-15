<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['codigo' => 'SUPERADMIN', 'nombre' => 'Super Administrador']);
        Role::create(['codigo' => 'ADMIN', 'nombre' => 'Administrador de sucursal']);
        Role::create(['codigo' => 'CAJERO', 'nombre' => 'Cajero']);
        Role::create(['codigo' => 'COCINA', 'nombre' => 'Personal de cocina']);
        Role::create(['codigo' => 'MESERO', 'nombre' => 'Mesero']);
    }
}
