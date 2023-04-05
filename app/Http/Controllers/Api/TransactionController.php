<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getNoTrx()
    {
        $now = Carbon::now()->format('Y-m-d');
        $transaction = Transaction::whereDate('created_at', $now)->orderBy('no_trx', 'DESC')->first();

        if ($transaction) {
            $noTrx = $transaction->no_trx + 1;
        } else {
            $noTrx = 1;
        }

        return response()->json([
            "no_trx" => $noTrx,
        ]);
    }

    public function checkIndividualTicket($ticket)
    {

        $transScanned = Transaction::where('ticket_code', $ticket)->where('tipe', 'individual')
            ->select(['amount', 'amount_scanned', 'status'])->first();

        if (!$transScanned) {
            return response()->json([
                "status" => "not found"
            ]);
        }

        if ($transScanned->status == "closed") {
            return response()->json([
                "status" => $transScanned->status,
                "count" => 0
            ]);
        }

        $counting = $transScanned->amount_scanned + 1;
        if ($transScanned->amount == $counting) {
            Transaction::where('ticket_code', $ticket)
                ->update([
                    "status" => "closed",
                    "amount_scanned" => $counting
                ]);
        } else {
            Transaction::where('ticket_code', $ticket)
                ->update([
                    "amount_scanned" => $counting
                ]);
        }

        return response()->json([
            "status" => $transScanned->status,
            "count" => $transScanned->amount - $counting
        ]);
    }

    public function checkGroupTicket(Request $request, $ticket)
    {
        $transScanned = Transaction::where('ticket_code', $ticket)->where('tipe', 'group')
            ->select(['amount', 'amount_scanned', 'status'])->first();

        Transaction::where('ticket_code', $ticket)
            ->update([
                "gate" => $request->gate,
            ]);

        if (!$transScanned) {
            return response()->json([
                "status" => "not found"
            ]);
        }


        if ($transScanned->status == "closed") {
            return response()->json([
                "status" => $transScanned->status,
                "count" => 0
            ]);
        }

        $counting = $transScanned->amount_scanned + 1;

        if ($transScanned->amount == $counting) {
            Transaction::where('ticket_code', $ticket)
                ->update([
                    "status" => "closed",
                    "amount_scanned" => $counting
                ]);
        } else {
            Transaction::where('ticket_code', $ticket)
                ->update([
                    "amount_scanned" => $counting
                ]);
        }

        return response()->json([
            "status" => $transScanned->status,
            "count" => $transScanned->amount - $counting
        ]);
    }
}
