@extends('layouts.AdminLTE.index')

@section('icon_page', 'user')

@section('title', 'Users')

@section('menu_pagina')	

@endsection

@section('content')   

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Summary Teknisi</h3>
        <a href="{{ route('order-item-teknisi.export-summary', request()->all()) }}" class="btn btn-info btn-sm">
            Export Summary
        </a>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Teknisi</th>
                    <th>Banyak Item</th>
                    <!-- <th>Masuk</th> -->
                    <th>Proses</th>
                    <th>Selesai</th>
                    <th>Cancel</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $s)
                    <tr>
                        <td>{{ $s->user->name ?? '-' }}</td>
                        <td>{{ $s->total }}</td>
                        <!-- <td><span class="label label-info">{{ $s->masuk }}</span></td> -->
                        <td><span class="label label-warning">{{ $s->proses }}</span></td>
                        <td><span class="label label-success">{{ $s->selesai }}</span></td>
                        <td><span class="label label-danger">{{ $s->cancel }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
            <h3 class="box-title">List Teknisi</h3>
            <a href="{{ route('order-item-teknisi.export', request()->all()) }}" class="btn btn-success btn-sm">
                Export List
            </a>

            <div class="box-tools pull-right">
                <form method="GET" action="{{ route('laporan.order-item-teknisi') }}" 
                    style="display:flex; gap:5px; align-items:center;">
                    <h5 class="input-sm">Tanggal&nbsp;Assign</h5>
                    <input type="date" name="start_date" class="form-control input-sm">
                    <input type="date" name="end_date" class="form-control input-sm">

                    <input type="text" name="search" class="form-control input-sm"
                        placeholder="Search" style="width:150px;">

                    <button type="submit" class="btn btn-default btn-sm">
                        <i class="fa fa-search"></i>
                    </button>

                </form>
            </div>
        </div>

            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Teknisi</th>
                            <th>Order Item ID</th>
                            <th>Nomer BON</th>
                            <th>Tanggal Bon</th>
                            <th>Tanggal Assign</th>
                            <th>Lihat Bon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->order_item_id }}</td>
                                <td>{{ optional(optional($item->orderItem)->order)->number_ticket ?? '-' }}</td>
                                <td>{{ optional(optional($item->orderItem)->order)->created_at->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $item->created_at ? $item->created_at->format('d-m-Y') : '-' }}</td>
                                <td>
                                    @if($item->orderItem)
                                        <a class="btn btn-primary btn-sm" style="margin:5px auto;"
                                        href="{{ route('orders.show', $item->orderItem->order_id) }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Data tidak ada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-right">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection