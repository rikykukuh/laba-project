@extends('layouts.AdminLTE.index')

@section('icon_page', 'whatsapp')
@section('title', 'WhatsApp V2')

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
        <div class="alert alert-warning"><i class="fa fa-warning"></i> {{ $apiError }}</div>
    @endif

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cog"></i> Pengaturan APIWA Pancalaba</h3>
            <span class="label label-info pull-right">
                {{ $apiKeySource === 'environment' ? 'Token dari Environment' : 'Token Terenkripsi' }}
            </span>
        </div>
        <form method="POST" action="{{ route('whatsapp-v2.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="box-body">
                @if ($errors->whatsappV2Settings->any())
                    <div class="alert alert-danger">
                        <ul style="margin-bottom: 0;">
                            @foreach ($errors->whatsappV2Settings->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="apiwa-base-url">Base URL API</label>
                            <input type="url" id="apiwa-base-url" name="base_url" class="form-control"
                                value="{{ old('base_url', $setting->base_url) }}" maxlength="2048" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="apiwa-api-key">API Key</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa {{ $hasApiKey ? 'fa-check text-green' : 'fa-times text-red' }}"></i>
                                </span>
                                <input type="password" id="apiwa-api-key" name="api_key" class="form-control"
                                    maxlength="4096" autocomplete="new-password"
                                    placeholder="{{ $hasApiKey ? 'API key sudah tersimpan' : 'Masukkan API key APIWA' }}">
                            </div>
                            @if ($hasApiKey)
                                @if ($apiKeySource === 'environment')
                                    <p class="help-block">API key dibaca dari <code>APIWA_API_KEY</code> di environment server.</p>
                                @endif
                                <div class="checkbox">
                                    <label><input type="checkbox" name="clear_api_key" value="1"> Hapus API key tersimpan</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-right">
                <a href="https://apiwa.pancalaba.id/docs" target="_blank" class="btn btn-default">
                    <i class="fa fa-book"></i> Dokumentasi API
                </a>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Simpan Pengaturan V2</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="small-box bg-green">
                <div class="inner"><h3>{{ $summary['connected'] }}</h3><p>Device V2 Terkoneksi</p></div>
                <div class="icon"><i class="fa fa-link"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="small-box bg-aqua">
                <div class="inner"><h3>{{ $summary['devices'] }}</h3><p>Total Device V2</p></div>
                <div class="icon"><i class="fa fa-whatsapp"></i></div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Daftar Device APIWA</h3>
            <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal"
                data-target="#add-apiwa-device-modal" {{ $hasApiKey ? '' : 'disabled' }}>
                <i class="fa fa-plus"></i> Tambah Device V2
            </button>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr><th>Nama</th><th>Nomor WhatsApp</th><th>Status</th><th>Terhubung</th><th>Terakhir Aktif</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($devices as $device)
                        @php
                            $status = strtolower((string) ($device['status'] ?? 'disconnected'));
                            $isConnected = $status === 'connected';
                            $statusClass = $isConnected ? 'label-success' : ($status === 'qr' || $status === 'connecting' ? 'label-warning' : 'label-danger');
                        @endphp
                        <tr>
                            <td>{{ $device['name'] ?? '-' }}</td>
                            <td>{{ $device['phone_number'] ?? '-' }}</td>
                            <td><span class="label {{ $statusClass }}">{{ strtoupper($status) }}</span></td>
                            <td>{{ !empty($device['connected_at']) ? \Carbon\Carbon::parse($device['connected_at'])->timezone('Asia/Jakarta')->format('d-m-Y H:i') : '-' }}</td>
                            <td>{{ !empty($device['last_seen_at']) ? \Carbon\Carbon::parse($device['last_seen_at'])->timezone('Asia/Jakarta')->format('d-m-Y H:i') : '-' }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                @if ($isConnected)
                                    <form method="POST" action="{{ route('whatsapp-v2.devices.disconnect', $device['id']) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-xs" onclick="return confirm('Putuskan device ini sementara?');">
                                            <i class="fa fa-unlink"></i> Disconnect
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-success btn-xs btn-connect-apiwa" data-device="{{ $device['id'] }}">
                                        <i class="fa fa-qrcode"></i> Connect
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('whatsapp-v2.devices.destroy', $device['id']) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Hapus device beserta sesi WhatsApp-nya?');">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted" style="padding:30px;">Belum ada device APIWA.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="add-apiwa-device-modal" role="dialog">
        <div class="modal-dialog"><div class="modal-content">
            <form method="POST" action="{{ route('whatsapp-v2.devices.store') }}">
                @csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Tambah Device WhatsApp V2</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="apiwa-device-name">Nama Device</label>
                        <input type="text" id="apiwa-device-name" name="name" class="form-control" value="{{ old('name') }}" minlength="2" maxlength="100" required>
                        <p class="help-block">Contoh: Customer Service, Kasir, atau Toko Utama.</p>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Buat Device</button></div>
            </form>
        </div></div>
    </div>

    <div class="modal fade" id="connect-apiwa-modal" role="dialog">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-whatsapp text-green"></i> Connect WhatsApp V2</h4></div>
            <div class="modal-body text-center">
                <div id="apiwa-loading" style="padding:35px 0;"><i class="fa fa-spinner fa-spin fa-3x text-green"></i><p style="margin-top:15px;">Menyiapkan koneksi APIWA...</p></div>
                <div id="apiwa-error" class="alert alert-danger" style="display:none;"></div>
                <div id="apiwa-connected" class="alert alert-success" style="display:none;"></div>
                <div id="apiwa-qr-wrapper" style="display:none;">
                    <p>Buka WhatsApp → <strong>Perangkat tertaut</strong> → <strong>Tautkan perangkat</strong>, lalu scan QR berikut.</p>
                    <div id="apiwa-qr" style="display:inline-block; padding:8px; border:1px solid #ddd; background:#fff;"></div>
                    <p class="text-muted">QR akan diperbarui otomatis sampai device terhubung.</p>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button></div>
        </div></div>
    </div>
@endsection

@section('layout_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        $(function () {
            var timer = null;
            var pending = false;
            var qrCode = null;

            function stopPolling() {
                if (timer) {
                    window.clearInterval(timer);
                    timer = null;
                }
                pending = false;
            }

            function renderQr(value) {
                if (!value || typeof QRCode === 'undefined') {
                    return;
                }
                if (!qrCode) {
                    qrCode = new QRCode(document.getElementById('apiwa-qr'), {
                        text: value,
                        width: 280,
                        height: 280,
                        correctLevel: QRCode.CorrectLevel.M
                    });
                } else {
                    qrCode.clear();
                    qrCode.makeCode(value);
                }
                $('#apiwa-loading').hide();
                $('#apiwa-qr-wrapper').show();
            }

            function poll(device) {
                stopPolling();
                timer = window.setInterval(function () {
                    if (pending || !$('#connect-apiwa-modal').hasClass('in')) {
                        return;
                    }
                    pending = true;
                    $.ajax({
                        url: @json(url('/whatsapp-v2/devices')) + '/' + encodeURIComponent(device) + '/status',
                        method: 'GET',
                        dataType: 'json',
                        headers: { 'Accept': 'application/json' }
                    }).done(function (response) {
                        if (response.connected) {
                            stopPolling();
                            $('#apiwa-loading, #apiwa-error, #apiwa-qr-wrapper').hide();
                            $('#apiwa-connected').text(response.message).show();
                            window.setTimeout(function () { window.location.reload(); }, 1200);
                            return;
                        }
                        renderQr(response.qr);
                    }).fail(function (xhr) {
                        var response = xhr.responseJSON || {};
                        $('#apiwa-loading').hide();
                        $('#apiwa-error').text(response.message || 'Status APIWA tidak dapat diperiksa.').show();
                    }).always(function () { pending = false; });
                }, 2500);
            }

            function connect(device) {
                stopPolling();
                $('#apiwa-error, #apiwa-connected, #apiwa-qr-wrapper').hide();
                $('#apiwa-loading').show();
                $('#connect-apiwa-modal').modal('show');

                $.ajax({
                    url: @json(url('/whatsapp-v2/devices')) + '/' + encodeURIComponent(device) + '/connect',
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    }
                }).done(function (response) {
                    if (response.connected) {
                        $('#apiwa-loading').hide();
                        $('#apiwa-connected').text(response.message).show();
                        window.setTimeout(function () { window.location.reload(); }, 1200);
                        return;
                    }
                    renderQr(response.qr);
                    poll(device);
                }).fail(function (xhr) {
                    var response = xhr.responseJSON || {};
                    $('#apiwa-loading').hide();
                    $('#apiwa-error').text(response.message || 'Koneksi APIWA gagal dimulai.').show();
                });
            }

            $('.btn-connect-apiwa').on('click', function () { connect($(this).data('device')); });
            $('#connect-apiwa-modal').on('hidden.bs.modal', stopPolling);

            @if ($errors->any() && old('name'))
                $('#add-apiwa-device-modal').modal('show');
            @endif
            @if (session('connect_apiwa_device'))
                connect(@json((string) session('connect_apiwa_device')));
            @endif
        });
    </script>
@endsection
