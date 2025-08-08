<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/plates');


Route::get("register",[AuthController::class,"index"])->name("register");
Route::post("register",[AuthController::class,"store"]);
Route::get("login", function () {
    return view('login');
});
Route::get("profile", function () {
    return view('customer.profile');
});
Route::post("login",[AuthController::class,'login'])->name("login");
Route::get('/plates/add', function () {
    return view('customer.add_plate');
})->name('plates.create');

Route::get('/profile/edit', function () {
    return view('edit-profile');
})->name('profile.edit');
Route::post('plates_add', [App\Http\Controllers\LicenseplateController::class, 'store'])->name('plates.store');
Route::post('plates/add/multiple', [App\Http\Controllers\LicenseplateController::class, 'multistore'])->name('multiplates.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('plates', [App\Http\Controllers\LicenseplateController::class, 'index'])->name('home');
Route::get('plates/export', [App\Http\Controllers\LicenseplateController::class, 'export'])->name('plates.export');
Route::get('plates/import', [App\Http\Controllers\LicenseplateController::class, 'import'])->name('plates.import');
Route::post('plates/import', [App\Http\Controllers\LicenseplateController::class, 'importStore'])->name('plates.import.store');

Route::get('plates/add/multiple', function () {
    return view('customer.add_multiple_plate');
})->name('plates.add.multiple');
Route::post('plates/import', [App\Http\Controllers\LicenseplateController::class, 'importStore'])->name('plates.import.store');