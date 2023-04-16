<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaction;
use App\Models\Ticket;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DetailTransactionController extends Controller
{
    public function index(Request $request,  $id)
    {
        if ($request->ajax()) {
            $data = DetailTransaction::where('transaction_id', $id);

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->ticket_id != 16) {
                        $actionBtn = '<button type="button" data-route="' . route('detail.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm"><i class="ion-ios-close"></i></button> <a href="' . route('detail.remove', $row->id) . '" class="btn btn-success btn-sm"><i class="ion-ios-remove"></i></a>';
                    } else {
                        $actionBtn = '';
                    }

                    return $actionBtn;
                })
                ->addColumn('ticket', function ($row) {
                    return $row->ticket->name;
                })
                ->addColumn('harga', function ($row) {
                    return number_format($row->ticket->harga, 0, ',', '.');
                })
                ->editColumn('total', function ($row) {
                    return number_format($row->ticket->harga * $row->qty, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $cek = DetailTransaction::where(['transaction_id' => $request->transaction, 'ticket_id' => $request->ticket])->first();

            if ($cek) {
                $qty = $cek->qty;

                $cek->update([
                    'qty' => $qty += 1
                ]);

                $cek->update([
                    'total' => Ticket::find($request->ticket)->harga * $cek->qty
                ]);
            } else {
                $trx = DetailTransaction::create([
                    'transaction_id' => $request->transaction,
                    'ticket_id' => $request->ticket,
                    'qty' => 1,
                    'total' => 0
                ]);

                $trx->update([
                    'total' => Ticket::find($request->ticket)->harga * $trx->qty
                ]);
            }

            $amount = DetailTransaction::where(['transaction_id' => $request->transaction])->sum('qty');

            if (!in_array($request->ticket, [14, 15])) {
                $asuransi = DetailTransaction::where(['transaction_id' => $request->transaction, 'ticket_id' => 16])->first();

                $asuransi->update([
                    'qty' => $amount - $asuransi->qty,
                ]);

                $asuransi->update([
                    'total' => $asuransi->qty * Ticket::find($asuransi->ticket_id)->harga
                ]);
            }

            $detail = DetailTransaction::where('transaction_id', $request->transaction)->get();
            $totalPrice = DetailTransaction::where('transaction_id', $request->transaction)->sum('total');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'detail' => $detail,
                'totalPrice' => number_format($totalPrice, 0, ',', '.'),
                'price' => $totalPrice
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy(DetailTransaction $detailTransaction)
    {
        try {
            DB::beginTransaction();

            $asuransi = DetailTransaction::where(['transaction_id' => $detailTransaction->transaction_id, 'ticket_id' => 16])->first();

            $amount = DetailTransaction::where(['transaction_id' => $detailTransaction->transaction_id])->count('qty');

            if ($amount == 1) {
                $asuransi->update([
                    'qty' => ($asuransi->qty - $detailTransaction->qty) + 1,
                ]);

                $asuransi->update([
                    'total' => $asuransi->qty * Ticket::find($asuransi->ticket_id)->harga
                ]);
            } else {
                $asuransi->update([
                    'qty' => $asuransi->qty - $detailTransaction->qty
                ]);

                $asuransi->update([
                    'total' => $asuransi->qty * Ticket::find($asuransi->ticket_id)->harga
                ]);
            }

            $detailTransaction->delete();

            DB::commit();
            return back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    public function save($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::find($id);
            $now = Carbon::now()->format('Y-m-d');
            $lastTrx = Transaction::whereDate('created_at', $now)->orderBy('no_trx', 'DESC')->first()->no_trx ?? 0;
            $tickets = [];
            $idtrx = [];
            $print = 1;
            $tipe = 'group';

            $totalHarga =  $transaction->detail()->whereNotIn('ticket_id', [14, 15])->sum('total');
            $parkir = $transaction->detail()->whereIn('ticket_id', [14, 15])->sum('total') ?? 0;
            $jasaRaharja = Ticket::find(16);

            $firstTrx = $transaction->detail()->whereNotIn('ticket_id', [16])->first();

            $transaction->update([
                'ticket_id' => $firstTrx->ticket_id,
                'no_trx' => $lastTrx += 1,
                'amount' => $firstTrx->qty,
                'is_active' => 1
            ]);

            $details = $transaction->detail()->whereNotIn('ticket_id', [14, 15])->get();
            $asuransi = $transaction->detail()->where('ticket_id', 16)->first();

            foreach ($details as $detail) {
                if ($detail->ticket_id != $transaction->ticket_id && $detail->ticket_id != 16) {
                    $newTrx = Transaction::create([
                        'user_id' => auth()->user()->id,
                        'ticket_id' => $detail->ticket_id,
                        'no_trx' => $transaction->no_trx += 1,
                        'ticket_code' => 'RIOWP' . Carbon::now('Asia/Jakarta')->format('Ymd') . rand(100, 999),
                        'tipe' => 'group',
                        'amount' => $detail->qty,
                        'is_active' => 1
                    ]);

                    $detail->update(
                        [
                            'transaction_id' => $newTrx->id,
                        ]
                    );

                    $newTrx->detail()->create([
                        'ticket_id' => $jasaRaharja->id,
                        'qty' => $newTrx->amount,
                        'total' => $newTrx->amount * $jasaRaharja->harga
                    ]);

                    $idtrx[] = $newTrx->id;
                }
            }

            $idtrx[] .= $transaction->id;

            $tickets = Transaction::whereIn('id', $idtrx)->get();

            $transaction->detail()->create([
                'ticket_id' => $jasaRaharja->id,
                'qty' => $transaction->amount,
                'total' => $transaction->amount * $jasaRaharja->harga
            ]);

            $asuransi->delete();

            DB::commit();

            return view('transaction.print', compact('tipe', 'print', 'tickets'));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function remove(DetailTransaction $detailTransaction)
    {
        try {
            DB::beginTransaction();

            if (!in_array($detailTransaction->ticket_id, [14, 15])) {
                $qty = $detailTransaction->qty;

                $detailTransaction->update([
                    'qty' => $qty - 1
                ]);

                $detailTransaction->update([
                    'total' => $detailTransaction->qty * $detailTransaction->ticket->harga
                ]);

                $asuransi = DetailTransaction::where(['transaction_id' => $detailTransaction->transaction_id, 'ticket_id' => 16])->first();

                $asuransi->update([
                    'qty' => $asuransi->qty - 1
                ]);

                $asuransi->update([
                    'total' => $asuransi->qty * $asuransi->ticket->harga
                ]);
            }

            DB::commit();

            return back();
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
