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
    <div class="row">
        <div style="max-width:80mm !important;  margin: 0 auto 0 auto; vertical-align: top; border-style: solid;border-width: 1px;">
            <div style="widht:80mm; margin-left: 15px; margin-top:10px;margin-bottom:0px">
                <div style="float: left;">
                    <img src="{{ asset('/images/heha.png') }}" width="80" width="80" alt="The Logo" class="brand-image" style="opacity: .8">
                </div>
                <div style="float: right; margin-right:5px">
                    <p style="font-size: 8.5pt;text-transform: uppercase; margin: 1px 1px 1px 1px;">RIO WATERPARK</p>
                    <p style="font-size: 8.5pt; margin-top:1px;margin-bottom: 5px;">{{ Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i:s') }} </p>
                    <p style="font-size: 8.5pt;margin: 1px 1px 5px 1px;"> - {{ $transaction->created_by }} </p>
                </div>
            </div>

            <div style="widht:80mm; font-size:8pt; padding:2mm 0 2mm 0; margin-top: 60px; margin-bottom: 0px; ">
                <hr style="border-style: dashed;">
                <p style="text-align:center;font-size:12pt;font-weight:bold;text-transform: uppercase;margin-bottom:0px">{{ $transaction->ticket->name }}</p>
                <br><br>
                <p style="font-size:10pt;margin-left:30px;margin-top:5px;margin-bottom:0px">Jumlah Ticket <span style="float: right; margin-right: 20px;">{{ $transaction->tipe == 'group' ? $transaction->amount . ' X ' . number_format($transaction->ticket->harga, 0, ',', '.') : $transaction->amount }}</span></p>

                <p style="font-size:10pt;margin-left:30px;margin-top:5px;margin-bottom:0px">Total (Asuransi & Parkir)<span style="float: right; margin-right: 20px; font-weight: bold;">Rp. {{ number_format($transaction->detail()->sum('total'), 0 , ',', '.') }}</span></p>
                <p style="font-size:10pt;margin-left:30px;margin-top:5px;margin-bottom:0px"></p>
                <br>
                <br><br>
                <!-- <p style="font-size:8.5pt;margin-top:5px;margin-left:30px;margin-bottom:0px">Total : Rp. {{ number_format($transaction->harga_ticket - $transaction->discount, 0, ',', '.') }}</p>
                <p style="font-size:8.5pt;margin-left:30px;margin-top:5px;margin-bottom:0px">Diterima : Rp. {{ number_format($transaction->cash, 0, ',', '.') }}</p>
                <p style="font-size:8.5pt;margin-left:30px;margin-top:5px">Kembali : Rp. {{ number_format($transaction->kembalian, 0, ',', '.') }}</p> -->
                <p style="font-size:10pt;text-align: center;margin-top:5px">*** Terima Kasih ***</p>
                <hr style="border-style: dashed;">
                <p style="font-size:8pt;text-align: center;margin-top:5px">RIO WATERPARK " Tiket berlaku satu kali masuk "</p>
                <hr style="border-style: dashed;">
                <p style="text-align: center;margin-top:15px;margin-bottom:15px">
                    {!! QrCode::size(100)->generate($transaction->ticket_code) !!} <br><br>
                    <span>{{ $transaction->ticket_code }}</span>
                </p>
                <hr style="border-style: dashed;">
                <p style="font-size:8pt;text-align: center;margin-top:5px;margin-bottom:0px">RIO WATERPARK " Barcode hanya buat buka gate "</p>
            </div>
            {{--
            <div style="text-align: center; margin-top:50px">
                <p>Struk Pembelian</p>
                <p style="font-size: 10pt">{!! QrCode::size(80)->generate($transaction->ticket_code) !!}</p>
            </div> --}}
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            window.print()
        })
    </script>
</body>

</html>