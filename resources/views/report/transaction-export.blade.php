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
            <td class="text-center">{{ App\Models\DetailTransaction::whereBetween('created_at', [$from, $to])->where('ticket_id', $ticket->id)->sum('qty') }}</td>
            <td class="text-center">{{ $ticket->harga, }}</td>
            <td class="text-end">
                {{ App\Models\DetailTransaction::whereBetween('created_at', [$from, $to])->where('ticket_id', $ticket->id)->sum('total')  ?? 0 }}
            </td>
        </tr>
        @endforeach
        <tr>
            <th>Total Amount :</th>
            <th class="text-center">
                <b>{{ App\Models\DetailTransaction::whereBetween('created_at', [$from, $to])->sum('qty') }}</b>
            </th>
            <th colspan="2" class="text-end">
                <b>{{ App\Models\DetailTransaction::whereBetween('created_at', [$from, $to])->whereIn('transaction_id', App\Models\Transaction::where('is_active', 1)->pluck('id'))->sum('total') }}</b>
            </th>
        </tr>
    </tbody>
</table>