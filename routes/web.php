<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


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