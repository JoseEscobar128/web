<?php

namespace App\Livewire\Admin\Employees;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Create extends Component
{
    public $nombre = '';
    public $apellido_paterno = '';
    public $apellido_materno = '';
    public $telefono = '';
    public $rfc = '';
    public $curp = '';
    public $nss = '';
    public $fecha_contratacion = '';
    public $estatus = 'activo';
    public $esta_activo = 1;
    public $huella = '';

    public function saveEmployee()
    {
        $validatedData = $this->validate([
            'nombre' => 'required|string',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
            'telefono' => 'required|string|regex:/^[0-9]{10}$/',
            'rfc' => 'required|string|max:13',
            'curp' => 'required|string|max:18',
            'nss' => 'required|string|max:11',
            'fecha_contratacion' => 'required|date',
            'estatus' => 'required|in:activo,baja',
            'esta_activo' => 'required|boolean',
            'huella' => 'nullable|string',
        ]);
        
        $payload = array_merge($validatedData, [
            'esta_activo' => $this->esta_activo,
        ]);
        
        $response = Http::authApi()->post('/api/v1/empleados/register', $payload);

        if ($response->successful()) {
            session()->flash('success', 'Empleado registrado exitosamente.');
            return redirect()->route('admin.employees.index');
        } else {
            $errorMessage = $response->json('message', 'Ocurrió un error inesperado.');
            session()->flash('error', 'Error al registrar empleado: ' . $errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.admin.employees.create')
            ->layout('layouts.app');
    }
}