<!DOCTYPE html>
<html>

<head>
    <title>Print QR</title>
    <style>
        @media print {
            .row {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    @if($tipe == 'group')
    @foreach($tickets as $ticket)
    <div class="row" style="max-height:150mm !important;">
        <div style="max-width:72mm !important;  margin: 0 auto 0 auto; vertical-align: top; border-style: solid;border-width: 1px;">
            <div style="widht:72mm; margin-top:10px;margin-bottom:0px">
                <div style="float: left;">
                    <img src="{{ asset('/images/heha.png') }}" width="80" width="80" alt="The Logo" class="brand-image" style="opacity: .8">
                </div>
                <div style="float: right; margin-right:5px">
                    <p style="font-size: 8.5pt;text-transform: uppercase; margin: 1px 1px 1px 1px;">RIO WATERPARK</p>
                    <p style="font-size: 8.5pt; margin-top:1px;margin-bottom: 5px;">{{ date('d/m/Y H:i:s', strtotime($ticket->created_at)) }} </p>
                    <p style="font-size: 8.5pt;margin: 1px 1px 5px 1px;"> </p>
                </div>
            </div>

            <div style="widht:72mm; font-size:8pt; padding:2mm 0 2mm 0; margin-top: 40px; margin-bottom: 0px; ">
                <hr style="border-style: dashed;">
                <p style="text-align:center;font-size:12pt;font-weight:bold;text-transform: uppercase;margin-bottom:0px">{{ $ticket->ticket->name }} <br> <span style="text-align:center;font-size:8pt;margin-bottom:0px">{{ $ticket->ticket_code }}</span>
                </p>
                <br><br>

                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px">Jumlah Ticket <span style="float: right; margin-right: 20px;">{{ $ticket->tipe == 'group' ? $ticket->amount . ' X ' . number_format($ticket->ticket->harga, 0, ',', '.') : $ticket->amount }}</span></p>

                @if($ticket->detail()->where('ticket_id',13)->first())
                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px">Asuransi <span style="float: right; margin-right: 20px;">{{ $ticket->tipe == 'group' ? $ticket->detail()->where('ticket_id',13)->first()->qty . ' X ' . number_format($ticket->detail()->where('ticket_id',13)->first()->ticket->harga, 0, ',', '.') : 0 }}</span></p>
                @endif

                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px">Jumlah <span style="float: right; margin-right: 20px;"> {{ number_format($ticket->detail()->sum('total'), 0 , ',', '.') }}</span></p>

                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px">Discount ({{ $ticket->discount }}%)
                    @php
                    $discount = $ticket->discount * $ticket->detail()->sum('total') / 100;
                    @endphp
                    <span style="float: right; margin-right: 20px;"> {{ number_format($discount, 0, ',', '.') }}</span>
                </p>

                @if($ticket->detail()->whereIn('ticket_id',[11,12])->first())
                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px">Parkir <span style="float: right; margin-right: 20px;">{{ number_format($ticket->detail()->whereIn('ticket_id',[11,12])->sum('total'), 0 , ',', '.') }}</span></p>
                @endif

                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px">Total Bayar <span style="float: right; margin-right: 20px; font-weight: bold;">Rp. {{ number_format($ticket->detail()->sum('total') - $discount, 0 , ',', '.') }}</span></p>

                <p style="font-size:10pt;margin-left:10px;margin-top:5px;margin-bottom:0px"></p>
                <br>
                <p style="font-size:10pt;text-align: center;margin-top:5px">*** Terima Kasih ***</p>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="qr-code" style="max-width:72mm !important;  margin: 0 auto 0 auto; vertical-align: top; border-style: solid;border-width: 1px;">
            <p style="font-size:8pt;text-align: center;margin-top:5px">RIO WATERPARK " Tiket berlaku satu kali masuk "</p>
            <hr style="border-style: dashed;">
            <p style="text-align: center;margin-top:15px;margin-bottom:15px">
                {!! QrCode::size(100)->generate($ticket->ticket_code) !!} <br><br>
                <span>{{ $ticket->ticket_code }}</span>
            </p>
            <hr style="border-style: dashed;">
            <p style="font-size:8pt;text-align: center;margin-bottom:0px">RIO WATERPARK " Barcode hanya buat buka gate "</p>
        </div>
    </div>
    @endforeach
    @endif

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            window.print();
            setTimeout(function() {
                document.location.href = "{{ route('transactions.create') }}";
            }, 3000)
        })
    </script>
</body>

</html>