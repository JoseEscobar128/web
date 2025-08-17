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

Route::get('/debug-url', function () {
    echo "URL generada por url('/'): " . url('/') . "<br>";
    echo "URL generada por asset('js/app.js'): " . asset('js/app.js');
});

// Pega esto al final de tu archivo routes/web.php

Route::get('/diagnostico-completo', function () {
    // Desactivamos el límite de tiempo de ejecución para esta ruta por si acaso
    set_time_limit(0);
    
    echo '<style>body { font-family: sans-serif; background: #f8f9fa; padding: 2em; } h1, h2 { color: #343a40; border-bottom: 2px solid #dee2e6; padding-bottom: 5px; } pre { background: #fff; border: 1px solid #ced4da; padding: 1em; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; } code { background: #e9ecef; padding: 2px 4px; border-radius: 3px; }</style>';
    echo '<h1>Diagnóstico Completo de la Aplicación</h1>';

    // --- SECCIÓN 1: ¿QUÉ LE DICE NGINX A PHP? ---
    echo '<h2>1. Información del Servidor (Vista de Nginx/PHP)</h2>';
    echo '<p>Estos son los datos crudos que PHP recibe del servidor. Claves como <code>HTTPS</code> y <code>HTTP_X_FORWARDED_PROTO</code> nos dicen si Nginx está comunicando que la conexión es segura. <code>SCRIPT_FILENAME</code> es crucial para saber si la ruta a tus archivos es correcta.</p>';
    echo '<pre>';
    print_r([
        'HTTPS'             => $_SERVER['HTTPS'] ?? 'NO PRESENTE',
        'X-Forwarded-Proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'NO PRESENTE',
        'SERVER_PORT'       => $_SERVER['SERVER_PORT'] ?? 'N/A',
        'REQUEST_URI'       => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'SCRIPT_FILENAME'   => $_SERVER['SCRIPT_FILENAME'] ?? 'N/A',
        'DOCUMENT_ROOT'     => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    ]);
    echo '</pre>';
    echo '<hr>';

    // --- SECCIÓN 2: ¿QUÉ INTERPRETA LARAVEL? ---
    echo '<h2>2. Información de la Petición (Vista de Laravel)</h2>';
    echo '<p>Así es como Laravel interpreta la información anterior. <code>isSecure()</code> debe ser <code>true</code>. Las URLs generadas por <code>url()</code> y <code>asset()</code> deben empezar con <code>https://</code>.</p>';
    echo '<pre>';
    print_r([
        'request()->isSecure()' => request()->isSecure() ? 'Sí (true)' : 'No (false)',
        'url()->current()'      => url()->current(),
        'asset("test.js")'      => asset('test.js'),
    ]);
    echo '</pre>';
    echo '<hr>';

    // --- SECCIÓN 3: ¿EL USUARIO TIENE SESIÓN? ---
    echo '<h2>3. Estado de Autenticación</h2>';
    echo '<p>Esto nos dice si, según Laravel, el usuario tiene una sesión activa. <code>auth()->check()</code> debe ser <code>true</code> después de iniciar sesión.</p>';
    echo '<pre>';
    print_r([
        'auth()->check()' => auth()->check() ? 'Sí (true)' : 'No (false)',
        'auth()->id()'    => auth()->id(),
        'auth()->user()'  => auth()->user() ? auth()->user()->toArray() : 'Nadie (null)',
    ]);
    echo '</pre>';
    echo '<hr>';

    // --- SECCIÓN 4: ¿QUÉ HAY EN LA SESIÓN? ---
    echo '<h2>4. Datos de la Sesión Actual</h2>';
    echo '<p>Aquí vemos todo lo que está guardado en la sesión actual. Después del login, deberíamos ver aquí el <code>access_token</code> y las claves de autenticación de Laravel.</p>';
    echo '<pre>';
    print_r(session()->all());
    echo '</pre>';
    echo '<hr>';

    // --- NUEVA SECCIÓN 5: ¿QUÉ CONFIGURACIÓN TIENE LIVEWIRE? ---
    echo '<h2>5. Diagnóstico de Livewire</h2>';
    echo '<p>Estos son los valores de configuración que Livewire está usando. La clave <code>app_url</code> es la que usa para generar la URL de su script dinámico (la que te da el error de Mixed Content). Si esta URL empieza con <code>http://</code>, hemos encontrado al culpable.</p>';
    
    // Verificamos si el archivo de configuración de Livewire existe antes de intentar leerlo.
    if (config()->has('livewire')) {
        echo '<pre>';
        print_r([
            'config("livewire.app_url")'   => config('livewire.app_url'),
            'config("livewire.asset_url")' => config('livewire.asset_url'),
        ]);
        echo '</pre>';
    } else {
        echo '<p><strong>Advertencia:</strong> El archivo <code>config/livewire.php</code> no parece estar publicado o cargado. Livewire podría estar usando valores por defecto.</p>';
    }
});
