<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\LicensePlate;

Route::redirect('/', '/plates');


Route::get("register",[AuthController::class,"index"])->name("register");
Route::post("register",[AuthController::class,"store"]);
Route::get("login", function () {
    return view('login');
});
Route::get("profile", function () {
    Auth::check() ? Auth::user() : abort(403, 'Unauthorized action.');


$myplates = LicensePlate::where('user_id', Auth::id())->get();





    return view('customer.profile', compact('myplates'));
})->name("profile");
Route::post("login",[AuthController::class,'login'])->name("login");
Route::get('/plates/add', function () {
    return view('customer.add_plate');
})->name('plates.create');

Route::get('/profile/edit', function () {
    return view('customer.edit_profile');
})->name('profile.edit');
Route::put('/profile/update', [AuthController::class, 'update'])->name('profile.update');
Route::post('plates_add', [App\Http\Controllers\LicenseplateController::class, 'store'])->name('plates.store');
Route::post('plates/add/multiple', [App\Http\Controllers\LicenseplateController::class, 'multistore'])->name('multiplates.store');
Route::post('/plates/ajaxProcess', [App\Http\Controllers\LicenseplateController::class, 'ajaxProcess'])->name('plates.ajaxProcess');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('plates', [App\Http\Controllers\LicenseplateController::class, 'index'])->name('home');
Route::get('plates/export', [App\Http\Controllers\LicenseplateController::class, 'export'])->name('plates.export');
Route::get('plates/import', [App\Http\Controllers\LicenseplateController::class, 'import'])->name('plates.import');
Route::post('plates/import', [App\Http\Controllers\LicenseplateController::class, 'importStore'])->name('plates.import.store');

Route::get('plates/add/multiple', function () {
    return view('customer.add_multiple_plate');
})->name('plates.add.multiple');
Route::post('plates/import', [App\Http\Controllers\LicenseplateController::class, 'importStore'])->name('plates.import.store');
Route::get('plates/{plate}/show', [App\Http\Controllers\LicenseplateController::class, 'show'])->name('plates.show');
Route::get('plates/{id}/edit', [App\Http\Controllers\LicenseplateController::class, 'edit'])->name('items.edit');
Route::put('/items/{id}', [App\Http\Controllers\LicenseplateController::class, 'update'])->name('items.update');
Route::get('/plates/summary', [App\Http\Controllers\LicenseplateController::class, 'summary'])->name('plates.summary');
Route::post('/updateMultiple', [App\Http\Controllers\LicenseplateController::class, 'updateMultiple']);
Route::get('plates/{id}/delete', [App\Http\Controllers\LicenseplateController::class, 'delete']);
Route::get('/plates/views', [App\Http\Controllers\LicenseplateController::class, 'viewAll'])->name('plates.summary');