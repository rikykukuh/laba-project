@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-basket')

@section('title', 'Reparasi')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('orders.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Reparasi
        </a>
    </li>

@endsection


@section('content')
    @if(Request::segment(1) === 'laporan')
    <div class="box box-primary">
        <div class="box-body">
            <form class="form-inline" id="form-filter">
                <input type="hidden" class="form-control" name="date_start" id="date_start" value="">
                <input type="hidden" class="form-control" name="date_end" id="date_end" value="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id='status' class="form-control" style="width: 200px" name="status">
                                <optgroup label="--Select Status--">
                                    <option value="ALL" {{ request()->get('status') == 'ALL' || request()->get('status') == '' ? 'selected' : ''  }}>All</option>
                                    <option value="DIPROSES" {{ request()->get('status') == 'DIPROSES' ? 'selected' : ''  }}>Diproses</option>
                                    <option value="DIAMBIL" {{ request()->get('status') == 'DIAMBIL' ? 'selected' : '' }}>Diambil</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date range:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="reservation">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="site_id">Cabang:</label>
                            <select class="form-control" id="site_id" name="site_id">
                                <option value="ALL" {{ request()->get('site_id') == 'ALL' || request()->get('site_id') == '' ? 'selected' : ''  }}>All</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}" {{ request()->get('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin: 15px auto;">
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm bg-maroon">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        {{--                        {!! $dataTable->table() !!} --}}
                        {{--  						<table id="table-order" class="table table-condensed table-bordered table-hover"> --}}
                        {{--  							<thead> --}}
                        {{--  								<tr> --}}
                        {{--  									<th class="text-center">#</th> --}}
                        {{--  									<th class="text-center">Nama Pelanggan</th> --}}
                        {{--  									 <th class="text-center">Total</th> --}}
                        {{--  									<th class="text-center">Status</th> --}}
                        {{--  									<th class="text-center">Tanggal Dibuat</th> --}}
                        {{--  									<th class="text-center">Aksi</th> --}}
                        {{--  								</tr> --}}
                        {{--  							</thead> --}}
                        {{--  							 --}}{{-- <tbody> --}}
                        {{-- 								 --}}{{-- @foreach ($orders as $order) --}}
                        {{-- 								 --}}{{-- 	@if ($order->id) --}}
                        {{-- 								 --}}{{-- 		<tr> --}}
                        {{--                               --}}{{--                <td>{{$order->customer->name}}</td> --}}
                        {{--                               --}}{{--                   <td>{{$order->orderItems->first()->note}}</td> --}}
                        {{--                               --}}{{--                   <td class="text-center">{{$order->orderItems->first()->total}}</td> --}}
                        {{--                               --}}{{--                <td class="text-center"> --}}
                        {{--                               --}}{{--                    @if ($order->status == 0) --}}
                        {{--                               --}}{{--                        @php --}}
                        {{--                               --}}{{--                            echo '<b>New</b>'; --}}
                        {{--                               --}}{{--                        @endphp --}}
                        {{--                               --}}{{--                    @endif --}}
                        {{--                               --}}{{--                    @if ($order->status == 1) --}}
                        {{--                               --}}{{--                        @php --}}
                        {{--                               --}}{{--                            echo '<b>Ready</b>'; --}}
                        {{--                               --}}{{--                        @endphp --}}
                        {{--                               --}}{{--                    @endif --}}
                        {{--                               --}}{{--                    @if ($order->status == 2) --}}
                        {{--                               --}}{{--                        @php --}}
                        {{--                               --}}{{--                            echo '<b>Paid</b>'; --}}
                        {{--                               --}}{{--                        @endphp --}}
                        {{--                               --}}{{--                    @endif --}}
                        {{--                               --}}{{--                    @if ($order->status == 3) --}}
                        {{--                               --}}{{--                        @php --}}
                        {{--                               --}}{{--                            echo '<b>Picked Up</b>'; --}}
                        {{--                               --}}{{--                        @endphp --}}
                        {{--                               --}}{{--                    @endif --}}
                        {{--                               --}}{{--                    {{ $order->status }} --}}
                        {{--                               --}}{{--                </td> --}}
                        {{--                               --}}{{--                <td class="text-center">{{ Carbon\Carbon::parse($order->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td> --}}
                        {{--                               --}}{{--                <td class="text-center"> --}}
                        {{--                               --}}{{--                    <a class="btn btn-default  btn-xs" href="{{ route('orders.show', $order->id) }}" title="Detail {{ $order->name }}"><i class="fa fa-eye">   </i></a> --}}
                        {{--                               --}}{{--                       <a class="btn btn-warning  btn-xs" href="{{ route('orders.edit', $order->id) }}" title="Edit {{ $order->name }}"><i class="fa fa-pencil"></i></a> --}}
                        {{--                               --}}{{--                    <form onsubmit="return confirm(''Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('orders.destroy', $order->id) }}" method="post" style="display: inline-block"> --}}
                        {{--                               --}}{{--                        @csrf --}}
                        {{--                               --}}{{--                        @method('DELETE') --}}
                        {{--                               --}}{{--                        <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $order->name}}" data-toggle="modal" data-target="#modal-delete-{{ $order->id }}"><i class="fa fa-trash"></i></button> --}}
                        {{--                               --}}{{--                    </form> --}}
                        {{--                               --}}{{--                </td> --}}
                        {{--                               --}}{{--            </tr> --}}
                        {{-- 								 --}}{{-- 	@endif --}}
                        {{-- 								 --}}{{-- @endforeach --}}
                        {{--  							 --}}{{-- </tbody> --}}
                        {{--  							<tfoot> --}}
                        {{--  								<tr> --}}
                        {{--                                      <th class="text-center">#</th> --}}
                        {{--                                       <th class="text-center">Nama Pelanggan</th> --}}
                        {{--  									 <th class="text-center">Total</th> --}}
                        {{--  									<th class="text-center">Status</th> --}}
                        {{--  									<th class="text-center">Tanggal Dibuat</th> --}}
                        {{--  									<th class="text-center">Aksi</th> --}}
                        {{--  								</tr> --}}
                        {{--  							</tfoot> --}}
                        {{--  						</table> --}}
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
        {{--        @if ($orders->hasPages()) --}}
        {{--            <div class="box-footer with-border"> --}}
        {{--                {{ $orders->links() }} --}}
        {{--            </div> --}}
        {{--        @endif --}}
    </div>

@endsection

@include('layouts.AdminLTE._includes._data_tables')

@section('scripts')
    <script src="{{ asset('public/vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable dan tambahkan elemen footer
            $('#table-service').DataTable().on('init', function() {
                $('<tfoot>').appendTo('#table-service');
                $(`#table-service tfoot`).html(`<tr><th colspan="5">Total</th><th id="total_bruto"></th><th id="total_discount"></th><th id="total_netto"></th><th id="total_vat"></th><th id="total_total"></th><th colspan="5"></th></tr>`);
            });

            // Panggil DataTable lagi setelah menambahkan elemen footer
            $('#table-service').DataTable().draw();
        });
    </script>
    @if(Request::segment(1) === 'laporan')
    <script type="text/javascript">
        $(function () {
            const date_start = "{{ request()->get('date_start') }}";
            const date_end = "{{ request()->get('date_end') }}";
            $('#date_start').val(date_start !== '' ? date_start : moment().subtract(30, 'days').format('YYYY-MM-DD'));
            $('#date_end').val(date_end !== '' ? date_end : moment().add(1, 'days').format('YYYY-MM-DD'));

            $('#status').select2();
            $('#status').on('select2:select', function(e) {
                $('#form-filter').submit();
            });

            $('#reservation').daterangepicker({
                startDate: date_start !== '' ? date_start : moment().subtract(30, 'days').format('YYYY-MM-DD'),
                endDate  : date_end !== '' ? date_end : moment().format('YYYY-MM-DD'),
                timePickerIncrement: 30,
                locale: { format: 'YYYY-MM-DD' }
            }, function(start, end, label){
                $('#date_start').val(start.format('YYYY-MM-DD'));
                $('#date_end').val(end.format('YYYY-MM-DD'));
                $('#form-filter').submit();
            });

            $('#site_id').select2();
            $('#site_id').on('select2:select', function(e) {
                $('#form-filter').submit();
            });

            let table = $('#table-service').DataTable({
                retrieve: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('orders.index') }}",
                    data: function (d) {
                        d.status = $('#status').val(),
                            d.search = $('input[type="search"]').val()
                    }
                },
                // columns: [
                //     {data: 'id', name: 'id'},
                //     {data: 'name', name: 'name'},
                //     {data: 'email', name: 'email'},
                //     {data: 'status', name: 'status'},
                // ]
            });

            // $('#form-filter').submit(function(e){
            //     // e.preventDefault();
            //     table.draw();
            // });
        });
    </script>
    @endif
    {{-- <script> --}}
    {{--     DataTable.ext.buttons.print = { --}}
    {{--         className: 'buttons-print', --}}

    {{--         text: function (dt) { --}}
    {{--             return  '<i class="fa fa-print"></i> ' + dt.i18n('buttons.print', 'Print'); --}}
    {{--         }, --}}

    {{--         action: function (e, dt, button, config) { --}}
    {{--             var url = _buildUrl(dt, 'print'); --}}
    {{--             window.location = url; --}}
    {{--         } --}}
    {{--     }; --}}
    {{-- </script> --}}
    {{-- <script> --}}
    {{--     $(function () { --}}
    {{--         $('#table-order').DataTable({ --}}
    {{--             dom: 'Bfrtip', --}}
    {{--             buttons: ['csv', 'excel', 'pdf', 'print'], --}}
    {{--             lengthMenu: [5, 10, 25, 50, 100, 250], --}}
    {{--             pageLength: 10, --}}
    {{--             createdRow: function(row, data, dataIndex) { --}}
    {{--                 $(row).find('td').addClass('text-center'); --}}
    {{--                 // $(row).find('td:eq(0)').addClass('text-center'); --}}
    {{--                 // $(row).find('td:eq(1)').addClass('text-center'); --}}
    {{--                 // $(row).find('td:eq(2)').addClass('text-center'); --}}
    {{--             }, --}}
    {{--             order: [ --}}
    {{--                 [2, "asc"] --}}
    {{--             ], --}}
    {{--             processing: true, --}}
    {{--             serverSide: true, --}}
    {{--             ajax: "{{ route('orders.index') }}", --}}
    {{--             columns: [ --}}
    {{--                 { --}}
    {{--                     data: 'DT_RowIndex', --}}
    {{--                     name: 'DT_RowIndex', --}}
    {{--                     orderable: false, --}}
    {{--                     searchable: false --}}
    {{--                 }, --}}
    {{--                 {data: 'name', name: 'name'}, --}}
    {{--                 {data: 'total', name: 'total'}, --}}
    {{--                 {data: 'status', name: 'status'}, --}}
    {{--                 {data: 'created_at', name: 'created_at'}, --}}
    {{--                 {data: 'action', name: 'action'}, --}}
    {{--             ] --}}
    {{--         }); --}}
    {{--     }); --}}
    {{-- </script> --}}
@endsection
