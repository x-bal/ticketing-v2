<?php

namespace App\Http\Controllers;

use App\Models\Penyewaan;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $breadcrumbs = ['Dashboard'];
        $now  = Carbon::now()->format('Y-m-d');
        $transaction = Transaction::whereDate('created_at', $now)->count();
        $sewa = Penyewaan::whereDate('created_at', $now)->count();
        $incometrx = array_sum(Transaction::withSum('detail', 'total')->whereDate('created_at', $now)->pluck('detail_sum_total')->toArray());
        $incomerent = Penyewaan::whereDate('created_at', $now)->sum('jumlah');

        return view('dashboard.index', compact('title', 'breadcrumbs', 'transaction', 'sewa', 'incometrx', 'incomerent'));
    }

    public function profile()
    {
        $title = 'Edit Profile';
        $breadcrumbs = ['Edit Profile'];
        $user = User::find(auth()->user()->id);

        return view('dashboard.profile', compact('title', 'breadcrumbs', 'user'));
    }

    public function update(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);

            $request->validate([
                'username' => 'required|string|unique:users,username,' . $user->id,
                'name' => 'required|string',
            ]);

            DB::beginTransaction();

            if ($request->password) {
                $password = bcrypt($request->password);
            } else {
                $password = $user->password;
            }

            $user->update([
                'username' => $request->username,
                'name' => $request->name,
                'password' => $password,
            ]);

            DB::commit();

            return back()->with('success', "Profile berhasil diupdate");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
