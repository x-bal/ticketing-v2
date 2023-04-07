<?php

use App\Http\Controllers\Api\TicketController as ApiTicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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
    return view('auth.login');
})->middleware('guest');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('users/get', [UserController::class, 'get'])->name('users.list');
    Route::resource('users', UserController::class);

    Route::get('tickets/get', [TicketController::class, 'get'])->name('tickets.list');
    Route::resource('tickets', TicketController::class);

    Route::get('sewa/get', [SewaController::class, 'get'])->name('sewa.list');
    Route::resource('sewa', SewaController::class);

    Route::get('members/get', [MemberController::class, 'get'])->name('members.list');
    Route::resource('members', MemberController::class);

    Route::get('transactions/get', [TransactionController::class, 'get'])->name('transactions.list');
    Route::get('transactions/{transaction:id}/print', [TransactionController::class, 'print'])->name('transactions.print');
    Route::get('report/transactions', [TransactionController::class, 'report'])->name('transactions.report');
    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::resource('transactions', TransactionController::class);

    Route::get('penyewaan/get', [PenyewaanController::class, 'get'])->name('penyewaan.list');
    Route::resource('penyewaan', PenyewaanController::class);
});

Route::get('/detail-group', [ApiTicketController::class, 'detailGroup']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
