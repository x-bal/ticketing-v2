<?php

namespace App\Http\Controllers;

use App\Models\HistoryPenyewaan;
use App\Models\Member;
use App\Models\Penyewaan;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenyewaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:penyewaan-access');
    }

    public function index()
    {
        $title = 'Data Penyewaan';
        $breadcrumbs = ['Master', 'Data Penyewaan'];
        $tickets = Sewa::get();

        return view('penyewaan.index', compact('title', 'breadcrumbs', 'tickets'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Penyewaan::latest();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '';
                    //<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('penyewaan.update', $row->id) . '" data-bs-toggle="modal">Edit</a>
                    if (auth()->user()->can('penyewaan-delete')) {
                        $actionBtn = '<button type="button" data-route="' . route('penyewaan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    }
                    return $actionBtn;
                })
                ->editColumn('ticket', function ($row) {
                    return $row->sewa->name;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->sewa->harga, 0, ',', '.');
                })
                ->editColumn('jumlah', function ($row) {
                    return 'Rp. ' . number_format($row->jumlah, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ticket' => 'required|numeric',
                'qty' => 'required|numeric',
                'metode' => 'required|string',
                'jumlah' => 'required|string',
            ]);

            DB::beginTransaction();

            if ($request->metode == 'tap') {
                $member = Member::where('rfid', $request->name)->first();

                if ($member) {
                    if ($member->saldo > $request->jumlah) {
                        $penyewaan = Penyewaan::create([
                            'sewa_id' => $request->ticket,
                            'qty' => $request->qty,
                            'metode' => $request->metode,
                            'jumlah' => $request->jumlah
                        ]);

                        $member->update([
                            'saldo' => $member->saldo - $request->jumlah
                        ]);

                        HistoryPenyewaan::create([
                            'penyewaan_id' => $penyewaan->id,
                            'member_id' => $member->id,
                        ]);

                        DB::commit();

                        return back()->with('success', "Penyewaan berhasil");
                    } else {
                        return back()->with('error', "Saldo anda tidak mencukupi");
                    }
                } else {
                    return back()->with('error', "Member tidak ditemukan");
                }
            } else {
                Penyewaan::create([
                    'sewa_id' => $request->ticket,
                    'qty' => $request->qty,
                    'metode' => $request->metode,
                    'jumlah' => $request->jumlah
                ]);

                DB::commit();

                return back()->with('success', "Penyewaan berhasil");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Penyewaan $penyewaan)
    {
        try {
            $history = HistoryPenyewaan::where('penyewaan_id', $penyewaan->id)->first();

            if ($history) {
                $member = Member::find($history->member_id);
                $member->update([
                    'saldo' => $member->saldo + $penyewaan->jumlah
                ]);

                $history->delete();
                $penyewaan->delete();

                DB::commit();

                return back()->with('success', "Penyewaan berhasil dihapus");
            } else {
                $penyewaan->delete();

                DB::commit();
                return back()->with('success', "Penyewaan berhasil dihapus");
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
