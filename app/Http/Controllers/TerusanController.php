<?php

namespace App\Http\Controllers;

use App\Models\Terusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerusanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => $request->nama_terusan,
        ]);

        try {
            DB::beginTransaction();

            $terusan = Terusan::create([
                'name' => $request->nama_terusan,
                'ticket_id' => $request->ticket
            ]);

            DB::commit();

            return back()->with('success', "{$terusan->name} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Terusan $terusan)
    {
        try {
            DB::beginTransaction();

            $terusan->delete();

            DB::commit();

            return back()->with('success', "Data terusan berhasil didelete");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
