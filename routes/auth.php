<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

   // Route::get('/', function () {
    //    return view('auth.login'); 
    //})->name('login');

    Route::get('verify-otp', function () {
        return view('auth.verify-otp');
    })->name('otp.verify');


});

Route::middleware('auth')->group(function () {

    Route::post('logout', \App\Http\Livewire\Actions\Logout::class)->name('logout');

});