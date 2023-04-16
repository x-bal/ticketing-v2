@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title">{{ $title }}</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-success" data-toggle="panel-reload"><i class="fa fa-redo"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-warning" data-toggle="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-danger" data-toggle="panel-remove"><i class="fa fa-times"></i></a>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <h2 class="mb-3">Total Price : <span id="price">Rp. {{ number_format($transaction->detail()->sum('total'), 0, ',','.') }}</span></h2>
            <div class="col-md-6">
                <table class="table table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

                <div class="form-group mb-3">
                    <label for="bayar">Bayar</label>
                    <input type="number" name="bayar" id="bayar" class="form-control" value="0" autofocus>
                </div>

                <div class="form-group mb-3">
                    <label for="kembali">Kembali</label>
                    <input type="number" name="kembali" id="kembali" class="form-control" value="0" readonly>
                </div>

                <input type="hidden" name="totalPrice" value="{{ $transaction->detail()->sum('total') }}" id="totalPrice">

                <div class="form-group">
                    <a href="{{ route('detail.save', $transaction->id) }}" class="btn btn-primary">Submit</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="content-ticket row">
                    @foreach($tickets as $ticket)
                    <div class="col-md-4 mb-2">
                        <button type="button" id="{{ $ticket->id }}" class="btn btn-primary text-center w-100 h-100 btn-ticket">
                            <span>
                                <span class="d-block" style="text-transform: uppercase;"><b>{{ $ticket->name }}</b></span>
                                <span class="d-block fs-12px opacity-7">{{ number_format($ticket->harga,0, ',', '.') }}</span>
                            </span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<form action="" class="d-none" id="form-delete" method="post">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>

<script>
    $(document).ready(function() {

        function getData() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('detail.list', $transaction->id) }}",
                deferRender: true,
                pagination: true,
                bDestroy: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        sortable: false,
                        searchable: false
                    },
                    {
                        data: 'ticket',
                        name: 'ticket',
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                    },
                    {
                        data: 'harga',
                        name: 'harga',
                    },
                    {
                        data: 'total',
                        name: 'total',
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ]
            });
        }

        getData()

        $(".btn-ticket").on('click', function() {
            let id = $(this).attr('id');
            let trx = "{{ $transaction->id }}";

            $.ajax({
                url: '/transaction/create',
                type: "GET",
                method: "GET",
                data: {
                    ticket: id,
                    transaction: trx
                },
                success: function(response) {
                    price = response.totalPrice;
                    $("#price").empty().append('Rp. ' + price)
                    $("#totalPrice").empty().val(response.price)
                    getData()
                }
            })
        })

        $("#datatable").on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let route = $(this).attr('data-route')
            $("#form-delete").attr('action', route)

            swal({
                title: 'Hapus data transaction?',
                text: 'Menghapus transaction bersifat permanen.',
                icon: 'error',
                buttons: {
                    cancel: {
                        text: 'Cancel',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        className: 'btn btn-danger',
                        closeModal: true
                    }
                }
            }).then((result) => {
                if (result) {
                    $("#form-delete").submit()
                } else {
                    $("#form-delete").attr('action', '')
                }
            });
        })

        $("#bayar").on('change', function() {
            let bayar = $(this).val();
            let price = $("#totalPrice").val()
            let kembali = parseInt(bayar - price)
            $("#kembali").val(kembali)
        })
    })
</script>

<script>

</script>
@endpush