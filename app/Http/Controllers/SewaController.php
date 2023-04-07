<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SewaController extends Controller
{
    public function index()
    {
        $title = 'Data Sewa';
        $breadcrumbs = ['Master', 'Data Sewa'];

        return view('sewa.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Sewa::orderBy('name', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('sewa.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('sewa.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->harga, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'harga' => 'required|numeric'
            ]);

            DB::beginTransaction();

            $sewa = Sewa::create($request->all());

            DB::commit();

            return redirect()->route('sewa.index')->with('success', "{$sewa->name} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Sewa $sewa)
    {
        return response()->json([
            'status' => 'success',
            'Sewa' => $sewa
        ], 200);
    }

    public function update(Request $request, Sewa $sewa)
    {
        try {
            DB::beginTransaction();

            $sewa->update($request->all());

            DB::commit();

            return redirect()->route('sewa.index')->with('success', "{$sewa->name} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Sewa $sewa)
    {
        try {
            DB::beginTransaction();

            $sewa->delete();

            DB::commit();

            return redirect()->route('sewa.index')->with('success', "{$sewa->name} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
