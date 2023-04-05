<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function getCode()
    {
        $tickets = Ticket::select(['id', 'name', 'harga'])->get();

        return $this->sendResponse($tickets, 'Tickets list');
    }

    public function detailGroup()
    {
        return view('detail');
    }

    public function detailGroupLast(Request $request)
    {
        $transaction = Transaction::where('status', 'open')
            ->where('tipe', 'group')
            ->where('gate', $request->gate)
            ->select(['ticket_code', 'amount', 'amount_scanned', 'nama_customer', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->first();

        $transaction['time'] = Carbon::parse($transaction->updated_at)->format('d/m/Y H:i:s');

        return response()->json($transaction);
    }
}
