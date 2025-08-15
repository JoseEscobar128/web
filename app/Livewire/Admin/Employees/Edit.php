<?php

namespace App\Livewire\Admin\Employees;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Edit extends Component
{
    public $employeeId;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $telefono;
    public $rfc;
    public $curp;
    public $nss;
    public $fecha_contratacion;
    public $estatus;
    public $esta_activo;
    public $huella;

    public function mount($id)
    {
        $this->employeeId = $id;
        $this->loadEmployee();
    }
    
    private function loadEmployee()
    {
        $response = Http::authApi()->get("/api/v1/empleados/{$this->employeeId}");
        
        if ($response->successful()) {
            $employee = $response->json()['data'];
            $this->nombre = $employee['nombre'];
            $this->apellido_paterno = $employee['apellido_paterno'];
            $this->apellido_materno = $employee['apellido_materno'];
            $this->telefono = $employee['telefono'];
            $this->rfc = $employee['rfc'];
            $this->curp = $employee['curp'];
            $this->nss = $employee['nss'];
            $this->fecha_contratacion = $employee['fecha_contratacion'];
            $this->estatus = $employee['estatus'];
            $this->esta_activo = $employee['esta_activo'];
            $this->huella = $employee['huella'] ?? '';
        } else {
            session()->flash('error', 'No se pudo cargar la información del empleado.');
            return redirect()->route('admin.employees.index');
        }
    }

    public function updateEmployee()
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
        
        $response = Http::authApi()->put("/api/v1/empleados/{$this->employeeId}", $payload);

        if ($response->successful()) {
            session()->flash('success', 'Empleado actualizado exitosamente.');
            return redirect()->route('admin.employees.index');
        } else {
            $errorMessage = $response->json('message', 'Ocurrió un error inesperado.');
            session()->flash('error', 'Error al actualizar empleado: ' . $errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.admin.employees.edit')->layout('layouts.app');
    }
}