<?php

namespace App\Http\Controllers;

use App\Exports\TransactionExport;
use App\Models\Penyewaan;
use App\Models\Ticket;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
        $date = $request->from ? Carbon::parse($request->from)->format('d/m/Y') . ' s.d ' . Carbon::parse($request->to)->format('d/m/Y') : Carbon::now()->format('d/m/Y');

        $title = 'Report Transaction ' . $date;
        $breadcrumbs = ['Master', 'Report Transaction'];

        return view('report.transaction', compact('title', 'breadcrumbs'));
    }

    public function transactionList(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now()->format('Y-m-d');

            if ($request->from && $request->to) {
                $to = Carbon::parse($request->to)->addDay(1)->format('Y-m-d');

                $data = Transaction::whereBetween('created_at', [$request->from, $to]);
            } else {
                $data = Transaction::whereDate('created_at', $now);
            }

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y H:i:s');
                })
                ->editColumn('ticket', function ($row) {
                    return $row->ticket->name;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->ticket->harga, 0, ',', '.');
                })
                ->editColumn('harga_ticket', function ($row) {
                    return 'Rp. ' . number_format($row->detail()->sum('total'), 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function penyewaan(Request $request)
    {
        $date = $request->from ? Carbon::parse($request->from)->format('d/m/Y') . ' s.d ' . Carbon::parse($request->to)->format('d/m/Y') : Carbon::now()->format('d/m/Y');

        $title = 'Report Penyewaan ' . $date;
        $breadcrumbs = ['Master', 'Report Penyewaan'];

        return view('report.penyewaan', compact('title', 'breadcrumbs'));
    }

    public function penyewaanList(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now()->format('Y-m-d');

            if ($request->from && $request->to) {
                $to = Carbon::parse($request->to)->addDay(1)->format('Y-m-d');

                $data = Penyewaan::whereBetween('created_at', [$request->from, $to]);
            } else {
                $data = Penyewaan::whereDate('created_at', $now);
            }

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y H:i:s');
                })
                ->editColumn('sewa', function ($row) {
                    return $row->sewa->name;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->sewa->harga, 0, ',', '.');
                })
                ->editColumn('total', function ($row) {
                    return 'Rp. ' . number_format($row->jumlah, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function rekapTransaction(Request $request)
    {
        $date = $request->from ? Carbon::parse($request->from)->format('d/m/Y') . ' s.d ' . Carbon::parse($request->to)->format('d/m/Y') : Carbon::now()->format('d/m/Y');
        $from = $request->from ? Carbon::parse($request->from)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $to = $request->to ? Carbon::parse($request->to)->addDay(1)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $title = 'Rekap Transaction ' . $date;
        $breadcrumbs = ['Master', 'Rekap Transaction'];
        $tickets = Ticket::whereNotIn('id', [14, 15, 16])->get();

        return view('report.rekap-transaction', compact('title', 'breadcrumbs', 'from', 'to', 'tickets'));
    }

    public function exportTransaction(Request $request)
    {
        $from = Carbon::parse(request('from'))->format('Y-m-d');
        $to = Carbon::parse(request('to'))->addDay(1)->format('Y-m-d');

        return Excel::download(new TransactionExport($from, $to), 'Report Transaction.xlsx');
    }
}
