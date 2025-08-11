<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\LicensePlate;

Route::redirect('/', '/plates');


Route::get("register",[AuthController::class,"index"]);
Route::post("register",[AuthController::class,"store"])->name("register");
Route::get("login", function () {
    return view('login');
});
Route::get("profile", function () {
    Auth::check() ? Auth::user() : abort(403, 'Unauthorized action.');
   $query = LicensePlate::query();

        // Filter: Start with
        if (request()->filled('city')) {
            $query->where('city', '=', request()->city);
        }
        if (request()->filled('status')) {
            $query->where('status', '=', request()->status);
        }

        if (request()->filled('start_with')) {
            $query->where('plate_number', 'like', request()->start_with . '%');
        }
        if (request()->filled('region')) {
            $query->where('region', '=', request()->region);
        }


        if (request()->filled('contain')) {
            $query->where('plate_number', 'like', '%' . request()->contain . '%');
        }

        if (request()->filled('end_with')) {
            $query->where('plate_number', 'like', '%' . request()->end_with);
        }

        if (request()->filled('length')) {
            $length = (int) request()->length;
            $query->whereRaw("LENGTH(REPLACE(REPLACE(plate_number, ' ', ''), '-', '')) = ?", [$length]);
        }
        if (request()->filled('min_price')) {
            $query->where('price', '>=', request()->min_price);
        }

        // Filter by max price
        if (request()->filled('max_price')) {
            $query->where('price', '<=', request()->max_price);
        }


        // Filter: Contain

        $cities = LicensePlate::select('city')
            ->whereNotNull('city')
            ->distinct()
            ->get();

        $regions = LicensePlate::select('region')
            ->whereNotNull('region')
            ->distinct()
            ->get();

        // Get the filtered plates
       // Ensure only plates of the authenticated user are fetched

      $myplates = $query->
        where('user_id', Auth::id())->
        get();







    return view('customer.profile', compact('myplates','cities', 'regions'));
})->name("profile");
Route::get("platesold", function () {
    Auth::check() ? Auth::user() : abort(403, 'Unauthorized action.');


$myplates = LicensePlate::where('user_id', Auth::id())
->where('status', 'Sold')
->get();





    return view('customer.mysold', compact('myplates'));
});
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
Route::get('/plates/export_pdf', [App\Http\Controllers\LicenseplateController::class, 'exportPdf'])->name('plates.export.pdf');
Route::get('plates/add/multiple', function () {
    return view('customer.add_multiple_plate');
})->name('plates.add.multiple');
Route::get('/plates/mypdf', [App\Http\Controllers\LicenseplateController::class, 'myPdf'])->name('plates.my.pdf');
Route::get('/plates/mycsv', [App\Http\Controllers\LicenseplateController::class, 'myCsv'])->name('plates.my.csv');
Route::get('/plates/export/pdf', [App\Http\Controllers\LicenseplateController::class, 'exportPdf']);




Route::get('/plates/import/form', [App\Http\Controllers\LicenseplateController::class, 'importPDFForm'])->name('plates.import.form');
Route::post('/plates/import-pdf', [App\Http\Controllers\LicenseplateController::class, 'importPDF'])->name('plates.importPDF');
Route::post('plates/import', [App\Http\Controllers\LicenseplateController::class, 'importStore'])->name('plates.import.store');
Route::get('plates/{plate}/show', [App\Http\Controllers\LicenseplateController::class, 'show'])->name('plates.show');
Route::get('plates/{id}/edit', [App\Http\Controllers\LicenseplateController::class, 'edit'])->name('items.edit');
Route::put('/items/{id}', [App\Http\Controllers\LicenseplateController::class, 'update'])->name('items.update');
Route::get('/plates/summary', [App\Http\Controllers\LicenseplateController::class, 'summary'])->name('plates.summary');
Route::post('/updateMultiple', [App\Http\Controllers\LicenseplateController::class, 'updateMultiple']);
Route::get('plates/{id}/delete', [App\Http\Controllers\LicenseplateController::class, 'delete']);
Route::get('/plates/views', [App\Http\Controllers\LicenseplateController::class, 'viewAll']);
Route::view('forgotpassword', 'forget')->name('forgotpassword');
Route::post('forget', [App\Http\Controllers\ForgetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\ForgetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset', [App\Http\Controllers\ForgetPasswordController::class, 'reset'])->name('password.update');
