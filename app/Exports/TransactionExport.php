<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionExport implements FromView
{
    protected $from, $to, $kasir;

    function __construct($from, $to, $kasir)
    {
        $this->from = $from;
        $this->to = $to;
        $this->kasir = $kasir;
    }

    public function view(): View
    {
        $tickets = Ticket::whereNotIn('id', [14, 15, 16])->get();

        return view('report.transaction-export', [
            'tickets' => $tickets,
            'from' => $this->from,
            'to' => $this->to,
            'kasir' => $this->kasir,
        ]);
    }
}
