@extends('layouts.AdminLTE.index')

@section('icon_page', 'file')

@section('title', 'Order Log')

@section('menu_pagina')

    <li role="presentation">
        <a href="#" class="link_menu_page">
            <i class="fa fa-file"></i> Order Log
        </a>
    </li>

@endsection

@section('layout_css')
    <link href="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mt-3" id="table-log-order">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 175px;">Date</th>
                                    <th class="text-center">Info</th>
                                    <th class="text-center" style="width: 175px;">Type Order</th>
                                    <th>Message</th>
                                    <th style="width: 100px;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $log['date'] ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="btn btn-xs {{ $log['type'] === 'info' ? 'btn-info' : ($log['type'] === 'error' ? 'btn-danger' : ($log['type'] === 'warning' ? 'btn-warning' : ($log['type'] === 'success' ? 'btn-success' : 'btn-secondary'))) }}">
                                            {{ ucwords($log['type']) ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <th>{{ $log['type_order'] ?? 'N/A' }}</th>
                                    <td>{{ $log['message'] ?? 'N/A' }}</td>
                                    <td>
                                        <pre style="width: 500px;height: 150px;">{{ json_encode($log['context'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('layouts.AdminLTE._includes._data_tables')

@section('scripts')
    <script src="{{ asset('public/vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset("plugins/jquery/jquery.min.js") }}"></script>

    <script src="{{ asset('plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/dataTables.buttons.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables.net-bs/js/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables.net-bs/js/buttons.colVis.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable dan tambahkan elemen footer
            $('#table-log-order').DataTable();
        });
    </script>
