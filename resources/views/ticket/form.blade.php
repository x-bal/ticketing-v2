@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
@push('style')

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
        <form action="{{ $action }}" method="post">
            @method($method)
            @csrf

            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $ticket->name ?? old('name') }}">

                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" class="form-control" value="{{ $ticket->harga ?? old('harga') }}">

                @error('harga')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="jenis">Jenis</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option disabled selected>-- Select Jenis --</option>
                    @foreach($jenis as $jns)
                    <option {{ $ticket->jenis_ticket_id == $jns->id ? 'selected' : '' }} value="{{ $jns->id }}">{{ $jns->nama_jenis }}</option>
                    @endforeach
                </select>

                @error('jenis')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="tripod">Tripod Id</label>
                <input type="number" name="tripod" id="tripod" class="form-control" value="{{ $ticket->tripod ?? old('tripod') }}">

                @error('tripod')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="terusan">Ticket Terusan</label>
                <select name="terusan[]" id="terusan" class="form-control multiple-select2" multiple>
                    @foreach($terusan as $ter)
                    <option {{ in_array($ter->id, $ticket->terusan()->pluck('terusan_id')->toArray()) ? 'selected' : '' }} value="{{ $ter->id }}">{{ $ter->name }}</option>
                    @endforeach
                </select>

                @error('terusan')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('/') }}plugins/select2/dist/js/select2.min.js"></script>

<script>
    $(".multiple-select2").select2({
        placeholder: "Pilih Tiket Terusan",
        allowClear: true
    })

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('roles.list') }}",
        deferRender: true,
        pagination: true,
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'action',
                name: 'action',
            },
        ]
    });

    $("#btn-add").on('click', function() {
        let route = $(this).attr('data-route')
        $("#form-role").attr('action', route)
    })

    $("#btn-close").on('click', function() {
        $("#form-role").removeAttr('action')
    })

    $("#datatable").on('click', '.btn-edit', function() {
        let route = $(this).attr('data-route')
        let id = $(this).attr('id')

        $("#form-role").attr('action', route)
        $("#form-role").append(`<input type="hidden" name="_method" value="PUT">`);

        $.ajax({
            url: "/roles/" + id,
            type: 'GET',
            method: 'GET',
            success: function(response) {
                let role = response.role;
                let permission = response.permission;

                $("#name").val(role.name)
                $('.multiple-select2').select2({
                    dropdownParent: $('#modal-dialog'),
                    placeholder: "Pilih Barang",
                    allowClear: true,
                }).val(permission).trigger('change')
            }
        })
    })

    $("#datatable").on('click', '.btn-detail', function() {
        let id = $(this).attr('id')

        $.ajax({
            url: "/roles/" + id,
            type: 'GET',
            method: 'GET',
            success: function(response) {
                let permissions = response.permissions;
                let append = ``;

                $.each(permissions, function(i, data) {
                    append += `<span class="badge bg-orange">` + data.name + `</span> `
                })

                $(".body-detail").empty().append(append)

            }
        })
    })

    $("#datatable").on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let route = $(this).attr('data-route')
        $("#form-delete").attr('action', route)

        swal({
            title: 'Hapus data role?',
            text: 'Menghapus role bersifat permanen.',
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