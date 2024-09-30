<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\EventController;
use GuzzleHttp\Middleware;

Route::get('/', [EventController::class, 'index']);
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth');
Route::get('/events/{id}', [EventController::class, 'show']);
Route::post('/events', [EventController::class, 'store']);
Route::delete('/events/{id}', [EventController::class, 'destroy'])->middleware('auth');
Route::get('events/edit/{id}', [EventController::class, 'edit'])->middleware('auth');
Route::put('events/update/{id}', [EventController::class, 'update'])->middleware('auth');

Route::get('/contact', function () {
    return view('contact');
});

// Route::get('/produtos', function () {
//     $busca = request('search');
//     return view('products', ['busca' => $busca]);
// });

// Route::get('/produtos/{id}', function ($id) {
//     return view('product', ['id' => $id]);
// });

// Route::get('/produtos_testes/{id?}', function ($id = null) {
//     return view('product', ['id' => $id]);
// });



Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');
Route::post('/events/join/{id}', [EventController::class, 'joinEvent'])->middleware('auth');

