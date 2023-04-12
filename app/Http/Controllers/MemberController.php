<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\CreateMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:member-access');
    }

    public function index()
    {
        $title = 'Data Member';
        $breadcrumbs = ['Master', 'Data Member'];

        return view('member.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Member::orderBy('id', 'asc');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('members.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('members.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->harga, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(CreateMemberRequest $request)
    {
        try {
            DB::beginTransaction();
            $attr = $request->except('tanggal_lahir');
            $attr['tgl_register'] = Carbon::now()->format('Y-m-d');
            $attr['tgl_expired'] = Carbon::now()->addMonth(6)->format('Y-m-d');
            $attr['tgl_lahir'] = request('tanggal_lahir');

            $member = Member::create($attr);

            DB::commit();

            return redirect()->route('members.index')->with('success', "Member {$member->nama} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Member $member)
    {
        return response()->json([
            'status' => 'success',
            'member' => $member
        ], 200);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        try {
            DB::beginTransaction();

            $member->update([
                'nama' => $request->nama,
                'no_ktp' => $request->no_ktp,
                'alamat' => $request->alamat,
                'tgl_lahir' => $request->tanggal_lahir,
            ]);

            DB::commit();

            return redirect()->route('members.index')->with('success', "Member {$member->nama} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Member $member)
    {
        try {
            DB::beginTransaction();

            $member->delete();

            DB::commit();

            return redirect()->route('members.index')->with('success', "Member {$member->nama} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function findOne(Request $request)
    {
        $member = Member::where('rfid', $request->rfid)->first();

        if ($member) {
            return response()->json([
                'status' => 'success',
                'member' => $member
            ]);
        } else {
            return response()->json([
                'status' => 'error',
            ]);
        }
    }
}
