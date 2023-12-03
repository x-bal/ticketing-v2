<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaction;
use App\Models\Ticket;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class DetailTransactionController extends Controller
{
    public function index(Request $request,  $id)
    {
        if ($request->ajax()) {
            $data = DetailTransaction::where('transaction_id', $id);

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $actionBtn = '<button type="button" data-route="' . route('detail.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm"><i class="fas fa-trash"></i></button>';
                    return $actionBtn;
                })
                ->editColumn('ticket', function ($row) {
                    return $row->ticket->name;
                })
                ->editColumn('qty', function ($row) {
                    return '<input type="number" name="qty" id="' . $row->id . '" class="form-control qty" value="' . $row->qty . '" autofocus>';
                })
                ->editColumn('harga', function ($row) {
                    return number_format($row->ticket->harga, 0, ',', '.');
                })
                ->editColumn('total', function ($row) {
                    return number_format($row->ticket->harga * $row->qty, 0, ',', '.');
                })
                ->rawColumns(['action', 'qty'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $cek = DetailTransaction::where(['transaction_id' => $request->transaction, 'ticket_id' => $request->ticket])->first();

            // if ($cek) {
            //     $qty = $cek->qty;

            //     $cek->update([
            //         'qty' => $qty += 1
            //     ]);

            //     $cek->update([
            //         'total' => Ticket::find($request->ticket)->harga * $cek->qty
            //     ]);
            // } else {
            DetailTransaction::create([
                'transaction_id' => $request->transaction,
                'ticket_id' => $request->ticket,
                'ticket_code' => 'TKT' . date('YmdHis') . rand(100, 999),
                'qty' => 1,
                'total' => Ticket::find($request->ticket)->harga,
            ]);


            // }

            $amount = DetailTransaction::where(['transaction_id' => $request->transaction])->sum('qty');

            // if (!in_array($request->ticket, [11, 12])) {
            //     $asuransi = DetailTransaction::where(['transaction_id' => $request->transaction, 'ticket_id' => 13])->first();

            //     if ($asuransi) {
            //         $asuransi->update([
            //             'qty' => $amount - $asuransi->qty,
            //         ]);

            //         $asuransi->update([
            //             'total' => $asuransi->qty * Ticket::find($asuransi->ticket_id)->harga
            //         ]);
            //     }
            // }

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

            $asuransi = DetailTransaction::where(['transaction_id' => $detailTransaction->transaction_id, 'ticket_id' => 13])->first();

            $amount = DetailTransaction::where(['transaction_id' => $detailTransaction->transaction_id])->count('qty');

            if ($asuransi) {
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

            $totalHarga =  $transaction->detail()->whereNotIn('ticket_id', [11, 12])->sum('total');
            $parkir = $transaction->detail()->whereIn('ticket_id', [11, 12])->sum('total') ?? 0;
            $jasaRaharja = Ticket::find(13);

            $firstTrx = $transaction->detail()->whereNotIn('ticket_id', [13])->first();

            $discount = request('discount') ?? 0;

            $transaction->update([
                'ticket_id' => $firstTrx->ticket_id,
                'amount' => $firstTrx->qty,
                'is_active' => 1,
                'discount' => $discount,
                'metode' => request('metode')
            ]);

            $details = $transaction->detail()->whereNotIn('ticket_id', [11, 12])->get();
            $asuransi = $transaction->detail()->where('ticket_id', 13)->first();

            foreach ($details as $detail) {
                if ($detail->ticket_id != $transaction->ticket_id && $detail->ticket_id != 13) {
                    $newTrx = Transaction::create([
                        'user_id' => auth()->user()->id,
                        'ticket_id' => $detail->ticket_id,
                        'no_trx' => $transaction->no_trx += 1,
                        'ticket_code' => 'RIOWP' . Carbon::now('Asia/Jakarta')->format('Ymd') . rand(100, 999),
                        'tipe' => 'group',
                        'amount' => $detail->qty,
                        'is_active' => 1,
                        'discount' => $discount,
                        'metode' => request('metode')
                    ]);

                    $detail->update(
                        [
                            'transaction_id' => $newTrx->id,
                        ]
                    );

                    $newTrx->update([
                        'disc' => $newTrx->detail()->sum('total') * $discount / 100
                    ]);

                    if ($asuransi) {
                        $newTrx->detail()->create([
                            'ticket_id' => $jasaRaharja->id,
                            'qty' => $newTrx->amount,
                            'total' => $newTrx->amount * $jasaRaharja->harga
                        ]);
                    }

                    $idtrx[] = $newTrx->id;
                }
            }

            $transaction->update([
                'disc' => $transaction->detail()->sum('total') * $discount / 100
            ]);

            $idtrx[] .= $transaction->id;

            $tickets = Transaction::whereIn('id', $idtrx)->get();

            if ($asuransi) {
                $transaction->detail()->create([
                    'ticket_id' => $jasaRaharja->id,
                    'qty' => $transaction->amount,
                    'total' => $transaction->amount * $jasaRaharja->harga
                ]);

                $asuransi->delete();
            }

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

            if (!in_array($detailTransaction->ticket_id, [11, 12])) {
                $qty = $detailTransaction->qty;

                $detailTransaction->update([
                    'qty' => $qty - 1
                ]);

                $detailTransaction->update([
                    'total' => $detailTransaction->qty * $detailTransaction->ticket->harga
                ]);

                $asuransi = DetailTransaction::where(['transaction_id' => $detailTransaction->transaction_id, 'ticket_id' => 13])->first();

                if ($asuransi) {
                    $asuransi->update([
                        'qty' => $asuransi->qty - 1
                    ]);

                    $asuransi->update([
                        'total' => $asuransi->qty * $asuransi->ticket->harga
                    ]);
                }
            }

            DB::commit();

            return back();
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function qty(Request $request)
    {
        try {
            DB::beginTransaction();

            $detail = DetailTransaction::find($request->id);
            $total = $request->qty * $detail->ticket->harga;
            $detail->update([
                'qty' => $request->qty,
                'total' => $total
            ]);

            $totalPrice = DetailTransaction::where('transaction_id', $detail->transaction_id)->sum('total');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'totalPrice' => number_format($totalPrice, 0, ',', '.'),
                'price' => $totalPrice
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    function testPrint()
    {
        try {
            // Enter the share name for your USB printer here
            // $connector = null;
            $connector = new WindowsPrintConnector("Receipt Printer");

            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);
            $printer->text("Hello World!\n");
            $printer->cut();

            /* Close printer */
            $printer->close();
        } catch (\Exception $e) {
            return "Couldn't print to this printer: " . $e->getMessage() . "\n";
        }
    }
}
