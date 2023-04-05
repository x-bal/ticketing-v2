<table class="table table-hover">
    <thead>
        <tr>
            <th colspan="4">Report Transaction Tanggal {{ Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($to)->subDay(1)->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>Jenis Ticket</th>
            <th class="text-center">Jumlah</th>
            <th>Harga Ticket</th>
            <th>Total Harga Ticket</th>
            <th>Rincian Discount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
        <tr>
            <td>{{ $ticket->name }}</td>
            <td class="text-center">{{ App\Models\Transaction::whereBetween('created_at', [$from, $to])->where('ticket_id', $ticket->id)->sum('amount') }}</td>
            <td>{{ $ticket->harga }}</td>
            <td>{{ App\Models\Transaction::whereBetween('created_at', [$from, $to])->where('ticket_id', $ticket->id)->sum('harga_ticket') }}</td>
            <td>{{ App\Models\Transaction::whereBetween('created_at', [$from, $to])->where('ticket_id', $ticket->id)->sum('discount') }}</td>
        </tr>
        @endforeach
        <tr>
            <th>Total Amount :</th>
            <th>{{ $jumlah }}</th>
            <th></th>
            <th></th>
            <th>{{ $total_amount }}</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>Jenis Pembayaran</th>
            <td class="text-center">Tunai :</td>
            <th></th>
            <th>{{ $tunai }}</th>
            <th></th>
        </tr>
        <tr>
            <th></th>
            <td class="text-center">Debit :</td>
            <th></th>
            <th>{{ $debit }}</th>
            <th></th>
        </tr>
        <tr>
            <th></th>
            <td class="text-center">Other :</td>
            <th></th>
            <th>{{ $other }}</th>
            <th></th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th colspan="4">Total Discount :</th>
            <th>{{ $discount }}</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th colspan="4">Grand Total :</th>
            <th>{{ $all }}</th>
        </tr>
        <tr>
            <th colspan="4">Grand Total Penerimaan Tunai :</th>
            <th>{{ $allTunai }}</th>
        </tr>
    </tbody>
</table>