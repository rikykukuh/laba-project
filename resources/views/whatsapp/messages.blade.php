@extends('layouts.AdminLTE.index')

@section('icon_page', 'comments')
@section('title', 'Riwayat Pesan WhatsApp')

@section('menu_pagina')
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Filter Pesan</h3></div>
        <div class="box-body">
            <form method="GET" action="{{ route('whatsapp.messages') }}">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control" name="status">
                            <option value="ALL" {{ $selectedStatus === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                            <option value="queued" {{ $selectedStatus === 'queued' ? 'selected' : '' }}>Terkirim ke Antrean</option>
                            <option value="failed" {{ $selectedStatus === 'failed' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="search" value="{{ $search }}"
                            placeholder="Cari target, No Bon, isi pesan, atau request ID">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Pesan yang Dikirim dari Aplikasi</h3>
            <span class="label label-default pull-right">{{ $messages->total() }} pesan</span>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr><th>Waktu</th><th>Target</th><th>No Bon</th><th>Pesan</th><th>Status</th><th>Pengirim</th><th>Request ID</th></tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td style="white-space: nowrap;">{{ $message->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $message->target }}</td>
                            <td>
                                @if ($message->order)
                                    <a href="{{ route('orders.show', $message->order_id) }}">{{ $message->order->number_ticket ?: $message->order_id }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="max-width: 360px; white-space: pre-line;">{{ $message->message }}</td>
                            <td><span class="label {{ $message->status === 'queued' ? 'label-success' : 'label-danger' }}">{{ $message->status === 'queued' ? 'Terkirim ke Antrean' : 'Gagal' }}</span></td>
                            <td>{{ optional($message->sender)->name ?: '-' }}</td>
                            <td>{{ $message->request_id ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted" style="padding: 30px;">Belum ada riwayat pesan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($messages->hasPages())
            <div class="box-footer clearfix"><div class="pull-right">{{ $messages->links() }}</div></div>
        @endif
    </div>
@endsection
