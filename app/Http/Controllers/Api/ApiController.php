<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Member;
use App\Models\Ticket;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getCode()
    {
        $tickets = Ticket::select(['id', 'name', 'harga'])->get();

        return $this->sendResponse($tickets, 'Tickets list');
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

    public function check(Request $request)
    {
        $transScanned = Transaction::where('ticket_code', $request->ticket)
            ->select(['amount', 'amount_scanned', 'status'])->first();

        if ($transScanned) {
            Transaction::where('ticket_code', $request->ticket)
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
                Transaction::where('ticket_code', $request->ticket)
                    ->update([
                        "status" => "closed",
                        "amount_scanned" => $counting
                    ]);
            } else {
                Transaction::where('ticket_code', $request->ticket)
                    ->update([
                        "amount_scanned" => $counting
                    ]);
            }

            return response()->json([
                "status" => $transScanned->status,
                "count" => $transScanned->amount - $counting
            ]);
        } else {
            $now = Carbon::now()->format('Y-m-d');

            $member = Member::where('rfid', $request->ticket)->first();

            if ($member) {
                if ($now > $member->tgl_register && $now <= $member->tgl_expired) {
                    $history = History::where('member_id', $member->id)->whereDate('created_at', $now)->count();

                    if ($history >= 2) {
                        return response()->json([
                            "status" => 'close',
                            "count" => 0
                        ]);
                    } else {
                        History::create([
                            'member_id' => $member->id,
                            'gate' => $request->gate
                        ]);

                        $newHistory = History::where('member_id', $member->id)->whereDate('created_at', $now)->count();

                        return response()->json([
                            "status" => 'open',
                            "count" => 2 - $newHistory
                        ]);
                    }
                }
            }
        }
    }
}
