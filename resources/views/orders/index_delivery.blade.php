@extends('layouts.AdminLTE.index')

@section('icon_page', 'truck')

@section('title', 'List Delivery')

@section('menu_pagina')

@endsection

@section('content')

<div class="box">
    <div class="box-header">
        <h3 class="box-title">List Delivery</h3>

        <div class="box-tools">
            <form method="GET" action="{{ route('laporan.delivery-list') }}" style="display:flex; gap:8px;">
                <select name="site_id" class="form-control input-sm">
                    <option value="">Semua Cabang</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" {{ (string) request('site_id') === (string) $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" class="form-control input-sm"
                    placeholder="Cari tiket / customer / no hp / alamat"
                    value="{{ request('search') }}">

                <button type="submit" class="btn btn-default btn-sm">
                    <i class="fa fa-search"></i>
                </button>

                <a href="{{ route('laporan.delivery-list') }}" class="btn btn-default btn-sm">Reset</a>
            </form>
        </div>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-hover table-bordered">
            <thead class="bg-navy">
                <tr>
                    <th class="text-center">No Tiket</th>
                    <th class="text-center">Customer</th>
                    <th class="text-center">Cabang</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Estimasi Ambil</th>
                    <th class="text-center">Driver</th>
                    <th class="text-center">Alamat Delivery</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $order)
                    @php
                        $numberTicket = $order->number_ticket
                            ?: (optional($order->site)->code
                                ? optional($order->site)->code . '-' . sprintf('%06d', $order->id)
                                : sprintf('%06d', $order->id));
                    @endphp
                    <tr>
                        <td class="text-center">
                            <b>{{ $numberTicket }}</b>
                        </td>
                        <td>
                            {{ optional($order->customer)->name ?? '-' }}<br>
                            <small>{{ optional($order->customer)->phone_number ?? '-' }}</small>
                        </td>
                        <td class="text-center">{{ optional($order->site)->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="label label-info">{{ $order->status ?? '-' }}</span>
                        </td>
                        <td class="text-center">{{ $order->estimate_take_item ?? '-' }}</td>
                        <td class="text-center">{{ optional($order->driver)->name ?? '-' }}</td>
                        <td style="max-width:300px;">
                            {{ $order->address ?? '-' }}
                            @if ($order->link_map_address)
                                <br>
                                <a href="{{ $order->link_map_address }}" target="_blank" class="btn btn-xs btn-default" style="margin-top:5px;">
                                    <i class="fa fa-map-marker"></i> Map
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada delivery yang belum diambil</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="box-footer clearfix">
        <div class="pull-right">
            {{ $data->appends(request()->all())->links() }}
        </div>
    </div>
</div>

@endsection
