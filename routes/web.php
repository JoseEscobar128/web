<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard;
use App\Livewire\Admin\Users\Index as UserIndex;
use App\Livewire\Admin\Users\Create as UserCreate;
use App\Livewire\Admin\Users\Edit as UserEdit;
use App\Livewire\Admin\Employees\Index as EmployeeIndex;
use App\Livewire\Admin\Employees\Create as EmployeeCreate;
use App\Livewire\Admin\Employees\Edit as EmployeeEdit;
use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Admin\Orders\Show as OrderShow;
use App\Livewire\PointOfSale;
use App\Livewire\KitchenDisplay;
use App\Livewire\MesasManagement;
use App\Livewire\Admin\Attendance\Index as AttendanceIndex;
use App\Livewire\Admin\Products\Index as ProductIndex;
use App\Livewire\Admin\Products\Create as ProductCreate;
use App\Livewire\Admin\Products\Edit as ProductEdit;
use App\Livewire\Admin\Categories\Index as CategoryIndex;
use App\Livewire\Admin\Categories\Create as CategoryCreate;
use App\Livewire\Admin\Categories\Edit as CategoryEdit;
use App\Livewire\Admin\Branches\Index as BranchIndex;
use App\Livewire\Admin\Branches\Create as BranchCreate;
use App\Livewire\Admin\Branches\Edit as BranchEdit;
use App\Livewire\ProfilePage;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    
     // Rutas para visitantes (sin sesión)
    Route::middleware('guest')->group(function() {
        Route::get('/', function () {
            if (auth()->check()) {
                return redirect()->route('dashboard');
            }
            return view('auth.login');
        })->name('home');

        Route::get('/login', [AuthController::class, 'redirectToProvider'])->name('login');
       Route::get('/callback', [AuthController::class, 'handleCallback']);
  

 });


    // Grupo de rutas protegidas que requieren un token de sesión
    Route::middleware('auth')->group(function () {
        
        // Rutas del Dashboard
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // CRUDs
        Route::get('/usuarios', UserIndex::class)->name('admin.users.index');
        Route::get('/usuarios/create', UserCreate::class)->name('admin.users.create');
        Route::get('/usuarios/{id}/edit', UserEdit::class)->name('admin.users.edit');

        Route::get('/empleados', EmployeeIndex::class)->name('admin.employees.index');
        Route::get('/empleados/create', EmployeeCreate::class)->name('admin.employees.create');
        Route::get('/empleados/{id}/edit', EmployeeEdit::class)->name('admin.employees.edit');

        Route::get('/productos', ProductIndex::class)->name('admin.products.index');
        Route::get('/productos/create', ProductCreate::class)->name('admin.products.create');
        Route::get('/productos/{id}/edit', ProductEdit::class)->name('admin.products.edit');

        Route::get('/categorias', CategoryIndex::class)->name('admin.categories.index');
        Route::get('/categorias/create', CategoryCreate::class)->name('admin.categories.create');
        Route::get('/categorias/{id}/edit', CategoryEdit::class)->name('admin.categories.edit');
        
        Route::get('/sucursales', BranchIndex::class)->name('admin.branches.index');
        Route::get('/sucursales/create', BranchCreate::class)->name('admin.branches.create');
        Route::get('/sucursales/{id}/edit', BranchEdit::class)->name('admin.branches.edit');

        // Rutas Operativas
        Route::get('/ordenes', OrderIndex::class)->name('admin.orders.index');
        Route::get('/ordenes/{id}', OrderShow::class)->name('admin.orders.show');
        Route::get('/pos', PointOfSale::class)->name('admin.pos.index');
        Route::get('/cocina', KitchenDisplay::class)->name('admin.kitchen.display');
        Route::get('/mesas', MesasManagement::class)->name('admin.mesas.index');
        Route::get('/asistencia', AttendanceIndex::class)->name('admin.attendance.index');

        // Perfil y Logout
        Route::get('/profile', ProfilePage::class)->name('profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::get('/preview/design', function () {
    return view('preview-design');
})->name('preview.design');

