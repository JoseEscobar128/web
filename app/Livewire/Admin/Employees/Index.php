<?php

namespace App\Livewire\Admin\Employees;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Index extends Component
{
    public array $employees = [];
    public ?int $employeeToDeleteId = null;
    public bool $showConfirmModal = false;

    public function mount()
    {
        $this->loadEmployees();
    }

    private function loadEmployees()
    {
        $response = Http::authApi()->get('/api/v1/empleados');

        if ($response->successful()) {
            $this->employees = $response->json()['data'];
        } else {
            session()->flash('error', 'Error al cargar los empleados. Inténtalo de nuevo.');
            $this->employees = [];
        }
    }

    public function confirmEmployeeDeletion($id)
    {
        $this->employeeToDeleteId = $id;
        $this->showConfirmModal = true;
    }

    public function deleteEmployee()
    {
        if ($this->employeeToDeleteId === null) {
            return;
        }

        $response = Http::authApi()->delete("/api/v1/empleados/{$this->employeeToDeleteId}");

        if ($response->successful()) {
            session()->flash('success', 'Empleado y usuario asociado eliminados correctamente.');
            $this->showConfirmModal = false;
            $this->loadEmployees();
        } else {
            session()->flash('error', 'No se pudo eliminar el empleado.');
        }
    }

    public function render()
    {
        return view('livewire.admin.employees.index')->layout('layouts.app');
    }
}