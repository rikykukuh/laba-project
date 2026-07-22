@extends('layouts.AdminLTE.index')

@section('icon_page', 'whatsapp')
@section('title', 'Device WhatsApp')

@section('menu_pagina')
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($apiError)
        <div class="alert alert-warning">
            <i class="fa fa-warning"></i> {{ $apiError }}
            @if (!config('services.fonnte.account_token'))
                Gunakan account token dari menu Settings Fonnte, bukan token device pengiriman pesan.
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-md-4"><div class="small-box bg-green"><div class="inner"><h3>{{ $summary['connected'] }}</h3><p>Device Terkoneksi</p></div><div class="icon"><i class="fa fa-link"></i></div></div></div>
        <div class="col-md-4"><div class="small-box bg-aqua"><div class="inner"><h3>{{ $summary['devices'] }}</h3><p>Total Device</p></div><div class="icon"><i class="fa fa-whatsapp"></i></div></div></div>
        <div class="col-md-4"><div class="small-box bg-yellow"><div class="inner"><h3>{{ $summary['messages'] }}</h3><p>Total Pesan</p></div><div class="icon"><i class="fa fa-comments"></i></div></div></div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Daftar Device</h3>
            <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#add-device-modal"
                {{ config('services.fonnte.account_token') ? '' : 'disabled' }}><i class="fa fa-plus"></i> Add Device</button>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead><tr><th>Nama</th><th>Nomor Device</th><th>Status</th><th>Package</th><th>Quota</th><th>Expired</th><th>Autoread</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($devices as $device)
                        @php
                            $isConnected = strtolower($device['status'] ?? '') === 'connect';
                            $expired = !empty($device['expired']) && is_numeric($device['expired'])
                                ? \Carbon\Carbon::createFromTimestamp($device['expired'])->timezone('Asia/Jakarta')->format('d-m-Y H:i')
                                : ($device['expired'] ?? '-');
                        @endphp
                        <tr>
                            <td>{{ $device['name'] ?? '-' }}</td><td>{{ $device['device'] ?? '-' }}</td>
                            <td><span class="label {{ $isConnected ? 'label-success' : 'label-danger' }}">{{ $isConnected ? 'Connect' : 'Disconnect' }}</span></td>
                            <td>{{ $device['package'] ?? '-' }}</td><td>{{ $device['quota'] ?? '-' }}</td><td>{{ $expired }}</td><td>{{ ucfirst($device['autoread'] ?? 'off') }}</td>
                            <td class="text-center">
                                @if ($isConnected)
                                    <form method="POST" action="{{ route('whatsapp.devices.disconnect', $device['device']) }}"
                                        onsubmit="return confirm('Disconnect device WhatsApp ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <i class="fa fa-unlink"></i> Disconnect
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted" style="padding: 30px;">Belum ada data device.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="add-device-modal" role="dialog">
        <div class="modal-dialog"><div class="modal-content">
            <form method="POST" action="{{ route('whatsapp.devices.store') }}">
                @csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Device WhatsApp</h4></div>
                <div class="modal-body">
                    <div class="form-group"><label for="device-name">Nama Device</label><input type="text" class="form-control" id="device-name" name="name" value="{{ old('name') }}" minlength="2" maxlength="30" required></div>
                    <div class="form-group"><label for="device-number">Nomor Device</label><input type="text" class="form-control" id="device-number" name="device" value="{{ old('device') }}" pattern="[0-9]{8,15}" maxlength="15" required><p class="help-block">Masukkan 8–15 digit. Nomor harus unik di Fonnte.</p></div>
                    <div class="checkbox"><label><input type="checkbox" name="autoread" value="1" {{ old('autoread') ? 'checked' : '' }}> Autoread</label></div>
                    <div class="checkbox"><label><input type="checkbox" name="personal" value="1" {{ old('personal') ? 'checked' : '' }}> Autoread chat personal</label></div>
                    <div class="checkbox"><label><input type="checkbox" name="group" value="1" {{ old('group') ? 'checked' : '' }}> Autoread chat group</label></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Device</button></div>
            </form>
        </div></div>
    </div>
@endsection

@section('layout_js')
    @if ($errors->any())
        <script>$(function () { $('#add-device-modal').modal('show'); });</script>
    @endif
@endsection
