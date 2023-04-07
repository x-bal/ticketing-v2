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
        <a href="#modal-dialog" id="btn-add" class="btn btn-primary mb-3" data-route="{{ route('penyewaan.store') }}" data-bs-toggle="modal"><i class="ion-ios-add"></i> Add Penyewaan</a>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">No</th>
                    <th class="text-nowrap">Nama Penyewaan</th>
                    <th class="text-nowrap">Harga</th>
                    <th class="text-nowrap">Qty</th>
                    <th class="text-nowrap">Metode</th>
                    <th class="text-nowrap">Total</th>
                    <th class="text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="modal-dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Form Penyewaan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="" method="post" id="form-penyewaan">
                    @csrf

                    <div class="modal-body row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="sewa">Jenis Sewa</label>
                                <select name="ticket" id="ticket" class="form-control">
                                    <option disabled selected>-- Select Penyewaan --</option>
                                    @foreach($tickets as $ticket)
                                    <option value="{{ $ticket->id }}" data-harga="{{ $ticket->harga }}">{{ $ticket->name }}</option>
                                    @endforeach
                                </select>

                                @error('ticket')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="qty">Qty</label>
                                <input type="number" name="qty" id="qty" class="form-control" value="1">

                                @error('qty')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="harga_ticket">Harga Tiket</label>
                                <input type="number" name="harga_ticket" id="harga_ticket" class="form-control" value="" readonly>

                                @error('harga_ticket')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="metode">Metode</label>
                                <select name="metode" id="metode" class="form-control">
                                    <option disabled selected>-- Pilih Metode --</option>
                                    <option value="cash">Cash</option>
                                    <option value="tap">Emoney</option>
                                </select>

                                @error('metode')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="" readonly>

                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" value="0" readonly>

                                @error('jumlah')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="javascript:;" id="btn-close" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('penyewaan.list') }}",
            deferRender: true,
            pagination: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    sortable: false,
                    searchable: false
                },
                {
                    data: 'ticket',
                    name: 'ticket'
                },
                {
                    data: 'harga',
                    name: 'harga'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'metode',
                    name: 'metode'
                },
                {
                    data: 'jumlah',
                    name: 'jumlah'
                },
                {
                    data: 'action',
                    name: 'action',
                },
            ]
        });

        $("#btn-add").on('click', function() {
            let route = $(this).attr('data-route')
            $("#form-penyewaan").attr('action', route)
        })

        $("#btn-close").on('click', function() {
            $("#form-penyewaan").removeAttr('action')
        })

        $("#datatable").on('click', '.btn-edit', function() {
            let route = $(this).attr('data-route')
            let id = $(this).attr('id')

            $("#form-penyewaan").attr('action', route)
            $("#form-penyewaan").append(`<input type="hidden" name="_method" value="PUT">`);

            $.ajax({
                url: "/tickets/" + id,
                type: 'GET',
                method: 'GET',
                success: function(response) {
                    let ticket = response.ticket;

                    $("#name").val(ticket.name)
                    $("#harga").val(ticket.harga)
                }
            })
        })

        $("#datatable").on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let route = $(this).attr('data-route')
            $("#form-delete").attr('action', route)

            swal({
                title: 'Hapus data penyewaan?',
                text: 'Menghapus penyewaan bersifat permanen.',
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

    <script>
        $(document).ready(function() {
            // $("#btn-add").on('click', function() {
            //     $.ajax({
            //         url: '/api/penyewaan/no-trx',
            //         type: "GET",
            //         method: "GET",
            //         success: function(response) {
            //             $("#no_trx").val(response.no_trx)
            //         }
            //     })

            //     $("#name").attr("autofocus", "autofocus")
            // })

            $("#ticket").on('change', function() {
                let element = $(this).find('option:selected');
                let harga = element.attr("data-harga");
                let amount = $("#qty").val();
                let harga_ticket = harga * amount;
                let jumlah = (harga * amount);

                $("#harga_ticket").val(harga_ticket)
                $("#jumlah").val(jumlah)
                $("#cash").val(jumlah)
            })

            $("#metode").on('change', function() {
                let metode = $(this).val()
                if (metode == 'tap') {
                    $("#name").removeAttr('readonly');
                } else {
                    $("#name").attr("readonly", "readonly")
                }
            })

            $("#qty").on('change', function() {
                let amount = $(this).val();
                let harga = $('#ticket option:selected').attr('data-harga');
                let harga_ticket = harga * amount;

                console.log(harga_ticket)
                let jumlah = (harga * amount);
                // $("#harga_ticket").val(harga_ticket)
                $("#jumlah").val(jumlah)
                $("#cash").val(jumlah)

            })

            $("#discount").on('change', function() {
                let discount = $(this).val();
                let harga = $("#harga_ticket").val();
                let jumlah = harga - discount;

                $("#jumlah").val("")
                $("#jumlah").val(jumlah)
                $("#cash").val(jumlah)
            })

            // $("#type_customer").on('change', function() {
            //     let type = $(this).val();

            // if (type == 'group') {
            //     $("#print").removeAttr('readonly')
            // } else {
            //     $("#amount").val(1)
            //     $("#print").val(1)
            //     $("#print").attr('readonly', 'readonly')
            // }
            // })

            // $("#metode").on('change', function() {
            //     let metode = $(this).val();

            //     if (metode != 'cash') {
            //         $("#cash").val(0);
            //         $("#cash").attr('readonly', 'readonly')
            //     } else {
            //         $("#cash").removeAttr('readonly')
            //     }
            // })

            $('#form-penyewaan').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            $("#name").on('keypress', function(e) {
                if (e.which == 13) {
                    let rfid = $(this).val();

                    $.ajax({
                        url: "/api/members",
                        type: "GET",
                        method: "GET",
                        data: {
                            rfid: rfid
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                let member = response.member;
                                $('#form-penyewaan').submit()
                            }

                            $("#rfid").val("")
                        },
                        error: function(response) {
                            $("#rfid").val("")
                        }
                    })
                }
            })
        })
    </script>
    @endpush