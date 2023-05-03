<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="5">Report Transaction Tanggal {{ Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ request('to') ? Carbon\Carbon::parse($to)->subDay(1)->format('d/m/Y') : Carbon\Carbon::parse($to)->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <th>Jenis Ticket</th>
            <th class="text-center">Jumlah</th>
            <th class="text-center">Harga Ticket</th>
            <th class="text-end">Total Harga Ticket</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
        <tr>
            <td>{{ $ticket->name }}</td>

            @if($kasir == 'all')
            @php
            $idtrx = App\Models\Transaction::where(['ticket_id' => $ticket->id, 'is_active' => 1])->whereBetween('created_at', [$from, $to])->pluck('id');
            @endphp
            @elseif($kasir != 'all')
            @php
            $idtrx = App\Models\Transaction::where(['ticket_id' => $ticket->id, 'is_active' => 1, 'user_id' => $kasir])->whereBetween('created_at', [$from, $to])->pluck('id');
            @endphp
            @else
            @php
            $idtrx = App\Models\Transaction::where(['ticket_id' => $ticket->id, 'is_active' => 1])->whereBetween('created_at', [$from, $to])->pluck('id');
            @endphp
            @endif

            <td class="text-center">{{ App\Models\DetailTransaction::whereIn('transaction_id', $idtrx)->sum('qty') }}</td>
            <td class="text-center">{{ $ticket->harga }}</td>
            <td class="text-end">
                {{ App\Models\DetailTransaction::whereIn('transaction_id', $idtrx)->sum('total') ?? 0 }}
            </td>
        </tr>
        @endforeach

        @if(request('from') && request('to') && request('kasir') == 'all')
        @php
        $idtrxx = App\Models\Transaction::where(['is_active' => 1])->whereBetween('created_at', [$from, $to])->pluck('id');
        $cashid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'cash'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $debitid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'debit'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $kreditid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'kredit'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $qrisid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'qris'])->whereBetween('created_at', [$from, $to])->pluck('id');
        @endphp
        @elseif(request('from') && request('to') && request('kasir') != 'all')
        @php
        $idtrxx = App\Models\Transaction::where(['is_active' => 1, 'user_id' => request('kasir')])->whereBetween('created_at', [$from, $to])->pluck('id');
        $cashid = App\Models\Transaction::where(['is_active' => 1, 'user_id' => request('kasir'), 'metode' => 'cash'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $debitid = App\Models\Transaction::where(['is_active' => 1, 'user_id' => request('kasir'), 'metode' => 'debit'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $kreditid = App\Models\Transaction::where(['is_active' => 1, 'user_id' => request('kasir'), 'metode' => 'kredit'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $qrisid = App\Models\Transaction::where(['is_active' => 1, 'user_id' => request('kasir'), 'metode' => 'qris'])->whereBetween('created_at', [$from, $to])->pluck('id');
        @endphp
        @else
        @php
        $idtrxx = App\Models\Transaction::where(['is_active' => 1])->whereBetween('created_at', [$from, $to])->pluck('id');
        $cashid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'cash'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $debitid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'debit'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $kreditid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'kredit'])->whereBetween('created_at', [$from, $to])->pluck('id');
        $qrisid = App\Models\Transaction::where(['is_active' => 1, 'metode' => 'qris'])->whereBetween('created_at', [$from, $to])->pluck('id');
        @endphp
        @endif

        <tr>
            <th>Total Penjualan :</th>
            <th class="text-center">
                <b>{{ App\Models\DetailTransaction::whereIn('transaction_id', $idtrxx)->sum('qty') }}</b>
            </th>
            <th></th>
            <th class="text-end">
                <b>{{ App\Models\DetailTransaction::whereIn('transaction_id', $idtrxx)->sum('total') }}</b>
            </th>
        </tr>

        <tr>
            <th colspan="3">Total Discount :</th>
            <th class="text-end">
                <b>{{ App\Models\Transaction::whereIn('id', $idtrxx)->sum('disc') }}</b>
            </th>
        </tr>

        <tr>
            <th>Metode Pembayaran :</th>
            <th class="text-center">Cash</th>
            <th colspan="2" class="text-end">
                {{ App\Models\DetailTransaction::whereIn('transaction_id', $cashid)->sum('total') }}
            </th>
        </tr>

        <tr>
            <th rowspan="3"></th>
            <th class="text-center">Debit</th>
            <th colspan="2" class="text-end">
                {{ App\Models\DetailTransaction::whereIn('transaction_id', $debitid)->sum('total') }}
            </th>
        </tr>

        <tr>
            <th class="text-center">Kredit</th>
            <th colspan="2" class="text-end">
                {{ App\Models\DetailTransaction::whereIn('transaction_id', $kreditid)->sum('total') }}
            </th>
        </tr>

        <tr>
            <th class="text-center">QRIS</th>
            <th colspan="2" class="text-end">
                {{ App\Models\DetailTransaction::whereIn('transaction_id', $qrisid)->sum('total') }}
            </th>
        </tr>

        <tr>
            <th colspan="3">Total Amount :</th>
            <th class="text-end">
                <b>{{ App\Models\DetailTransaction::whereIn('transaction_id', $idtrxx)->sum('total') - App\Models\Transaction::whereIn('id', $idtrxx)->sum('disc') }}</b>
            </th>
        </tr>
    </tbody>
</table>