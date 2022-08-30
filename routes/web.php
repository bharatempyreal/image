<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Auth::routes(['verify' => true]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/image', [App\Http\Controllers\HomeController::class, 'image'])->name('image');

Route::delete('/remove/{id}', [App\Http\Controllers\HomeController::class, 'remove'])->name('remove');

Route::post('/delete', [App\Http\Controllers\HomeController::class, 'deleteImage'])->name('delete');

Route::get('/table', [App\Http\Controllers\HomeController::class, 'table'])->name('table');
Route::post('/getimg', [App\Http\Controllers\HomeController::class, 'getImg'])->name('getimg');
Route::post('/sortable', [App\Http\Controllers\HomeController::class, 'sortable'])->name('sortable');



Route::get('image/{filename}', function($filename) {
    $path = storage_path('app/public/uploads/' . $filename);
    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('image.displayImage');