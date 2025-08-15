<?php

namespace App\Http\Livewire\Actions;

use Illuminate\Support\Facades\Auth;

class Logout
{
    /**
     * Log the user out of the application.
     */
    public function __invoke()
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}