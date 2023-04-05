<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\TicketRequest;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    public function index()
    {
        $title = 'Data Ticket';
        $breadcrumbs = ['Master', 'Data Ticket'];

        return view('ticket.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Ticket::orderBy('name', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('tickets.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('tickets.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->harga, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        $title = 'Add Ticket';
        $breadcrumbs = ['Master', 'Add Ticket'];
        $action = route('tickets.store');
        $method = 'POST';
        $ticket = new Ticket();

        return view('ticket.form', compact('title', 'breadcrumbs', 'action', 'method', 'ticket'));
    }

    public function store(TicketRequest $request)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::create($request->all());

            DB::commit();

            return redirect()->route('tickets.index')->with('success', "Ticket {$ticket->name} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Ticket $ticket)
    {
        return response()->json([
            'status' => 'success',
            'ticket' => $ticket
        ], 200);
    }

    public function edit(Ticket $ticket)
    {
        $title = 'Edit Ticket';
        $breadcrumbs = ['Master', 'Edit Ticket'];
        $action = route('tickets.update', $ticket->id);
        $method = 'PUT';

        return view('ticket.form', compact('title', 'breadcrumbs', 'action', 'method', 'ticket'));
    }

    public function update(TicketRequest $request, Ticket $ticket)
    {
        try {
            DB::beginTransaction();

            $ticket->update($request->all());

            DB::commit();

            return redirect()->route('tickets.index')->with('success', "Ticket {$ticket->name} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Ticket $ticket)
    {
        try {
            DB::beginTransaction();

            $ticket->delete();

            DB::commit();

            return redirect()->route('tickets.index')->with('success', "Ticket {$ticket->name} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
