<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReportExport implements FromView
{
    protected $from, $to;

    function __construct($from, $to,)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        $tickets = Ticket::get();
        $total_amount = Transaction::whereBetween('created_at', [$this->from, $this->to])->sum('harga_ticket');
        $jumlah = Transaction::whereBetween('created_at', [$this->from, $this->to])->sum('amount');
        $tunai = Transaction::whereBetween('created_at', [$this->from, $this->to])->where('metode', 'tunai')->sum('harga_ticket');
        $debit = Transaction::whereBetween('created_at', [$this->from, $this->to])->where('metode', 'debit')->sum('harga_ticket');
        $other = Transaction::whereBetween('created_at', [$this->from, $this->to])->where('metode', 'other')->sum('harga_ticket');
        $discount = Transaction::whereBetween('created_at', [$this->from, $this->to])->sum('discount');
        $all = $total_amount - $discount;
        $allTunai = $tunai - $discount;


        return view('transaction.export', [
            'tickets' => $tickets,
            'total_amount' => $total_amount,
            'jumlah' => $jumlah,
            'tunai' => $tunai,
            'debit' => $debit,
            'other' => $other,
            'all' => $all,
            'allTunai' => $allTunai,
            'discount' => $discount,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }
}
