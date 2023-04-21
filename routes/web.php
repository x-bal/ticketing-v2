<?php

use App\Http\Controllers\Api\TicketController as ApiTicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailTransactionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\TerusanController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('profile', [DashboardController::class, 'update'])->name('profile.update');

    Route::get('users/get', [UserController::class, 'get'])->name('users.list');
    Route::resource('users', UserController::class);

    Route::get('tickets/get', [TicketController::class, 'get'])->name('tickets.list');
    Route::get('tickets/find/{ticket:id}', [TicketController::class, 'find'])->name('tickets.find');
    Route::resource('tickets', TicketController::class);

    Route::get('terusan/get', [TerusanController::class, 'get'])->name('terusan.list');
    Route::resource('terusan', TerusanController::class);

    Route::get('sewa/get', [SewaController::class, 'get'])->name('sewa.list');
    Route::resource('sewa', SewaController::class);

    Route::get('members/get', [MemberController::class, 'get'])->name('members.list');
    Route::resource('members', MemberController::class);

    Route::get('transactions/get', [TransactionController::class, 'get'])->name('transactions.list');
    Route::get('transactions/{transaction:id}/print', [TransactionController::class, 'print'])->name('transactions.print');
    Route::get('transaction/create', [DetailTransactionController::class, 'store']);
    Route::resource('transactions', TransactionController::class);

    Route::get('detail/{id}/list', [DetailTransactionController::class, 'index'])->name('detail.list');
    Route::delete('detail/{detailTransaction:id}', [DetailTransactionController::class, 'destroy'])->name('detail.destroy');
    Route::get('detail/{id}/save', [DetailTransactionController::class, 'save'])->name('detail.save');
    Route::get('detail/{detailTransaction:id}/remove', [DetailTransactionController::class, 'remove'])->name('detail.remove');
    Route::get('/detail/qty', [DetailTransactionController::class, 'qty'])->name('detail.qty');

    Route::get('penyewaan/get', [PenyewaanController::class, 'get'])->name('penyewaan.list');
    Route::resource('penyewaan', PenyewaanController::class);

    Route::get('topup/get', [TopupController::class, 'get'])->name('topup.list');
    Route::resource('topup', TopupController::class);

    Route::get('roles/get', [RoleController::class, 'get'])->name('roles.list');
    Route::resource('roles', RoleController::class);

    Route::get('permissions/get', [PermissionController::class, 'get'])->name('permissions.list');
    Route::resource('permissions', PermissionController::class);

    Route::get('report/transactions', [ReportController::class, 'transaction'])->name('reports.transactions');
    Route::get('report/transactions-list', [ReportController::class, 'transactionList'])->name('reports.transaction-list');
    Route::get('rekap/transactions', [ReportController::class, 'rekapTransaction'])->name('rekap.transactions');
    Route::get('export-transaction', [ReportController::class, 'exportTransaction'])->name('transactions.export');
    Route::get('report/penyewaan', [ReportController::class, 'penyewaan'])->name('reports.penyewaan');
    Route::get('report/penyewaan-list', [ReportController::class, 'penyewaanList'])->name('reports.penyewaan-list');
    Route::get('rekap/penyewaan', [ReportController::class, 'rekapPenyewaan'])->name('rekap.penyewaan');
    Route::get('export-penyewaan', [ReportController::class, 'exportPenyewaan'])->name('penyewaan.export');
});

Route::get('/detail-group', [ApiTicketController::class, 'detailGroup']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('reset', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
});
