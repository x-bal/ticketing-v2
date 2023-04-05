<?php

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/transactions/no-trx', [TransactionController::class, 'getNoTrx']);
Route::get('ticket/{ticket}/individu-check', [TransactionController::class, 'checkIndividualTicket']);
Route::get('ticket/{ticket}/group-check', [TransactionController::class, 'checkGroupTicket']);
Route::get('ticket/code', [TicketController::class, 'getCode']);
Route::get('ticket/{id}/printQR', [TicketController::class, 'printQR']);
Route::get('ticket/group', [TicketController::class, 'detailGroup']);
Route::get('ticket/group-last', [TicketController::class, 'detailGroupLast']);
Route::post('tickets/print-qr', [TicketController::class, 'print_qr']);
