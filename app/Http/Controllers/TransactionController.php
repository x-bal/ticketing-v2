<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Http\Requests\Transaction\CreateTransactionRequest;
use App\Models\DetailTransaction;
use App\Models\Sewa;
use App\Models\Ticket;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:transaction-access');
    }

    public function index()
    {
        $title = 'Data Transaction';
        $breadcrumbs = ['Master', 'Data Transaction'];
        $tickets = Ticket::get();

        return view('transaction.index', compact('title', 'breadcrumbs', 'tickets'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::where('is_active', 1)->orderBy('no_trx', 'DESC');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    //<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('transactions.update', $row->id) . '" data-bs-toggle="modal">Edit</a>
                    $actionBtn = '<a href="' . route("transactions.print", $row->id) . '" class="btn btn-sm btn-primary">Print</a> <button type="button" data-route="' . route('transactions.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->addColumn('ticket', function ($row) {
                    return $row->ticket->name;
                })
                ->addColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->ticket->harga, 0, ',', '.');
                })
                ->editColumn('harga_ticket', function ($row) {
                    return 'Rp. ' . number_format($row->detail()->sum('total'), 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        $title = 'Add Transaction';
        $breadcrumbs = ['Master', 'Add Transaction'];
        $action = route('transactions.store');
        $method = 'POST';

        if (request('tipe') == 'sewa') {
            $tickets = Sewa::get();
        } else {
            $tickets = Ticket::get();
        }

        $now = Carbon::now()->format('Y-m-d');
        $lastTrx = Transaction::whereDate('created_at', $now)->latest()->first();
        if ($lastTrx) {
            $notrx = $lastTrx->no_trx + 1;
        } else {
            $notrx = 1;
        }

        $active = Transaction::whereDate('created_at', $now)->where('is_active', 0)->latest()->first();
        if ($active) {
            $transaction = $active;
        } else {
            $transaction = Transaction::create([
                'ticket_id' => 0,
                'user_id' => auth()->user()->id,
                'no_trx' => $notrx,
                'ticket_code' => 'RIOWP' . Carbon::now('Asia/Jakarta')->format('dmY') . rand(100, 999)
            ]);

            DetailTransaction::create([
                'transaction_id' => $transaction->id,
                'ticket_id' => 16,
                'qty' => 1,
                'total' => Ticket::find(16)->harga
            ]);
        }


        return view('transaction.form', compact('title', 'breadcrumbs', 'action', 'method', 'transaction', 'tickets'));
    }

    public function store(CreateTransactionRequest $request)
    {
        try {
            DB::beginTransaction();

            $transactions = [];

            $attr = $request->except('name', 'ticket', 'type_customer', 'print', 'jumlah');
            // $tipe = $request->type_customer;
            $tipe = 'group';
            $attr['ticket_id'] = $request->ticket;
            $attr['tipe'] = $tipe;
            $attr['nama_customer'] = $request->name;
            $attr['metode'] = $request->metode;
            $attr['cash'] = $request->cash;
            $attr['amount'] = 1;
            $attr['harga_ticket'] = $request->harga_ticket;
            $attr['kembalian'] = $request->kembalian;
            $attr['discount'] = ($request->harga_ticket * $request->discount) / 100;
            $attr['user_id'] = auth()->user()->id;

            $now = Carbon::now()->format('Y-m-d');
            $print = 1;
            $transactions = [];
            $lastTrx = Transaction::whereDate('created_at', $now)->latest()->first();

            if ($lastTrx) {
                $notrx = $lastTrx->no_trx + 1;
            } else {
                $notrx = 1;
            }

            if ($tipe == 'individual') {
                for ($i = 0; $i < $request->amount; $i++) {
                    $attr['no_trx'] = $notrx++;
                    $attr['ticket_code'] = 'TKT' . Carbon::now('Asia/Jakarta')->format('dmY') . rand(1000, 9999);

                    $transaction = Transaction::create($attr);

                    $transactions[] = $transaction->id;
                }
            } else {
                $attr['no_trx'] = $notrx;
                $attr['ticket_code'] = 'GRP' . Carbon::now('Asia/Jakarta')->format('dmY') . rand(1000, 9999);
                $attr['amount'] = $request->amount;

                $transaction = Transaction::create($attr);

                $transactions = $transaction->id;
            }

            DB::commit();

            $tickets = [];

            if ($tipe == 'individual') {
                foreach ($transactions as $transaction) {
                    $tickets[] =   Transaction::where('id', $transaction)->first();
                }
            } else {
                $tickets[] = $transaction;
            }

            return view('transaction.print', compact('tipe', 'print', 'tickets'));
        } catch (\Throwable $th) {
            return $th->getMessage();
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        return response()->json([
            'status' => 'success',
            'ticket' => $transaction
        ], 200);
    }

    public function edit(Transaction $transaction)
    {
        $title = 'Edit Transaction';
        $breadcrumbs = ['Master', 'Edit Transaction'];
        $action = route('transactions.update', $transaction->id);
        $method = 'PUT';

        return view('transaction.form', compact('title', 'breadcrumbs', 'action', 'method', 'transaction'));
    }

    public function update(CreateTransactionRequest $request, Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            $transaction->update($request->all());

            DB::commit();

            return redirect()->route('transactions.index')->with('success', "Transaction berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            foreach ($transaction->detail as $detail) {
                $detail->delete();
            }

            $transaction->delete();

            DB::commit();

            return redirect()->route('transactions.index')->with('success', "Transaction berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Transaction $transaction)
    {
        return view('transaction.single-print', compact('transaction'));
    }

    public function report(Request $request)
    {
        $title = 'Report Transaction';
        $breadcrumbs = ['Master', 'Report Transaction'];
        $transactions = Transaction::get();
        $from = $request->from ? Carbon::parse($request->from)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $to = $request->to ? Carbon::parse($request->to)->addDay(1)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $tickets = Ticket::get();

        return view('transaction.report', compact('title', 'breadcrumbs', 'transactions', 'from', 'to', 'tickets'));
    }

    public function export(Request $request)
    {
        $from = Carbon::parse(request('from'))->format('Y-m-d');
        $to = Carbon::parse(request('to'))->addDay(1)->format('Y-m-d');

        return Excel::download(new ReportExport($from, $to), 'Report Transaction.xlsx');
    }
}
