@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
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
        <form action="" method="get">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="from">From</label>
                        <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="to">To</label>
                        <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-1">
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </div>
                </div>
            </div>
        </form>
        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">No</th>
                    <th class="text-nowrap">Tanggal</th>
                    <th class="text-nowrap">Ticket</th>
                    <th class="text-nowrap">Metode</th>
                    <th class="text-nowrap">Harga</th>
                    <th class="text-nowrap">Qty</th>
                    <th class="text-nowrap">Total</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{ asset('/') }}plugins/pdfmake/build/pdfmake.min.js"></script>
<script src="{{ asset('/') }}plugins/pdfmake/build/vfs_fonts.js"></script>
<script src="{{ asset('/') }}plugins/jszip/dist/jszip.min.js"></script>
<script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>

<script>
    let from = $("#from").val();
    let to = $("#to").val();

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('reports.penyewaan-list') }}",
            type: "GET",
            data: {
                "from": from,
                "to": to,
            }
        },
        deferRender: true,
        pagination: true,
        dom: '<"row"<"col-sm-5"B><"col-sm-7"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
        buttons: [{
                extend: 'excel',
                className: 'btn-sm btn-success'
            },
            {
                extend: 'pdf',
                className: 'btn-sm btn-danger'
            },
            {
                extend: 'print',
                className: 'btn-sm btn-info'
            }
        ],
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                sortable: false
            },
            {
                data: 'tanggal',
                name: 'tanggal'
            },
            {
                data: 'sewa',
                name: 'sewa'
            },
            {
                data: 'metode',
                name: 'metode'
            },
            {
                data: 'harga',
                name: 'harga'
            },
            {
                data: 'qty',
                name: 'qty',
            },
            {
                data: 'total',
                name: 'total',
            },
        ]
    });

    $("#btn-add").on('click', function() {
        let route = $(this).attr('data-route')
        $("#form-sewa").attr('action', route)
    })

    $("#btn-close").on('click', function() {
        $("#form-sewa").removeAttr('action')
    })

    $("#datatable").on('click', '.btn-edit', function() {
        let route = $(this).attr('data-route')
        let id = $(this).attr('id')

        $("#form-sewa").attr('action', route)
        $("#form-sewa").append(`<input type="hidden" name="_method" value="PUT">`);

        $.ajax({
            url: "/sewa/" + id,
            type: 'GET',
            method: 'GET',
            success: function(response) {
                let sewa = response.sewa;

                $("#name").val(sewa.name)
                $("#harga").val(sewa.harga)
                $("#device").val(sewa.device)
            }
        })
    })

    $("#datatable").on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let route = $(this).attr('data-route')
        $("#form-delete").attr('action', route)

        swal({
            title: 'Hapus data ticket?',
            text: 'Menghapus ticket bersifat permanen.',
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
</script>
@endpush