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
            @if (!$hasAccountToken && $canManageFonnteSettings)
                Isi account token pada form pengaturan di bawah. Account token berbeda dengan token device pengiriman.
            @endif
        </div>
    @endif

    @if ($canManageFonnteSettings)
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cog"></i> Pengaturan Fonnte</h3>
            <span class="label label-info pull-right">Disimpan di Database</span>
        </div>
        <form method="POST" action="{{ route('whatsapp.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="box-body">
                @if ($errors->whatsappSettings->any())
                    <div class="alert alert-danger">
                        <strong>Pengaturan belum tersimpan:</strong>
                        <ul style="margin-bottom: 0;">
                            @foreach ($errors->whatsappSettings->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-muted">
                    Token disimpan terenkripsi. Kosongkan kolom token jika tidak ingin mengganti token yang sudah tersimpan.
                </p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="device-token">Token Device Pengiriman</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa {{ $hasDeviceToken ? 'fa-check text-green' : 'fa-times text-red' }}"></i>
                                </span>
                                <input type="password" id="device-token" name="device_token" class="form-control"
                                    maxlength="4096" autocomplete="new-password"
                                    placeholder="{{ $hasDeviceToken ? 'Token sudah tersimpan' : 'Masukkan token device Fonnte' }}">
                            </div>
                            @if ($hasDeviceToken)
                                <div class="checkbox">
                                    <label><input type="checkbox" name="clear_device_token" value="1"> Hapus token device tersimpan</label>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account-token">Account Token</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa {{ $hasAccountToken ? 'fa-check text-green' : 'fa-times text-red' }}"></i>
                                </span>
                                <input type="password" id="account-token" name="account_token" class="form-control"
                                    maxlength="4096" autocomplete="new-password"
                                    placeholder="{{ $hasAccountToken ? 'Account token sudah tersimpan' : 'Masukkan account token Fonnte' }}">
                            </div>
                            @if ($hasAccountToken)
                                <div class="checkbox">
                                    <label><input type="checkbox" name="clear_account_token" value="1"> Hapus account token tersimpan</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="send-endpoint">URL Kirim Pesan</label>
                            <input type="url" id="send-endpoint" name="send_endpoint" class="form-control"
                                value="{{ old('send_endpoint', $setting->send_endpoint) }}" maxlength="2048" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="country-code">Kode Negara</label>
                            <input type="text" id="country-code" name="country_code" class="form-control"
                                value="{{ old('country_code', $setting->country_code) }}" pattern="[0-9]{1,4}" maxlength="4" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="qr-endpoint">URL Connect/QR</label>
                            <input type="url" id="qr-endpoint" name="qr_endpoint" class="form-control"
                                value="{{ old('qr_endpoint', $setting->qr_endpoint) }}" maxlength="2048" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="get-devices-endpoint">URL Daftar Device</label>
                            <input type="url" id="get-devices-endpoint" name="get_devices_endpoint" class="form-control"
                                value="{{ old('get_devices_endpoint', $setting->get_devices_endpoint) }}" maxlength="2048" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="add-device-endpoint">URL Tambah Device</label>
                            <input type="url" id="add-device-endpoint" name="add_device_endpoint" class="form-control"
                                value="{{ old('add_device_endpoint', $setting->add_device_endpoint) }}" maxlength="2048" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="disconnect-endpoint">URL Disconnect Device</label>
                            <input type="url" id="disconnect-endpoint" name="disconnect_endpoint" class="form-control"
                                value="{{ old('disconnect_endpoint', $setting->disconnect_endpoint) }}" maxlength="2048" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-right">
                <button type="submit" class="btn btn-info">
                    <i class="fa fa-save"></i> Simpan Pengaturan WhatsApp
                </button>
            </div>
        </form>
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
                {{ $hasAccountToken ? '' : 'disabled' }}><i class="fa fa-plus"></i> Add Device</button>
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
                                    <button type="button" class="btn btn-success btn-xs btn-connect-device"
                                        data-device="{{ $device['device'] }}">
                                        <i class="fa fa-qrcode"></i> Connect
                                    </button>
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

    <div class="modal fade" id="connect-device-modal" role="dialog" aria-labelledby="connect-device-title">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="connect-device-title">
                        <i class="fa fa-whatsapp text-green"></i> Connect WhatsApp
                    </h4>
                </div>
                <div class="modal-body text-center">
                    <div id="connect-device-loading" style="padding: 40px 0;">
                        <i class="fa fa-spinner fa-spin fa-3x text-green"></i>
                        <p style="margin-top: 15px;">Meminta QR Code dari Fonnte...</p>
                    </div>
                    <div id="connect-device-error" class="alert alert-danger" style="display: none;"></div>
                    <div id="connect-device-connected" class="alert alert-success" style="display: none;"></div>
                    <div id="connect-device-qr-wrapper" style="display: none;">
                        <p>
                            Buka WhatsApp di ponsel → <strong>Perangkat tertaut</strong> →
                            <strong>Tautkan perangkat</strong>, lalu scan QR Code berikut.
                        </p>
                        <img id="connect-device-qr" alt="QR Code koneksi WhatsApp"
                            style="width: 280px; max-width: 100%; border: 1px solid #ddd; padding: 8px;">
                        <p class="text-muted" style="margin-top: 12px;">
                            QR Code dapat kedaluwarsa. Klik Connect kembali untuk membuat QR baru.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.reload();">
                        <i class="fa fa-refresh"></i> Refresh Status
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('layout_js')
    <script>
        $(function () {
            var connectStatusTimer = null;
            var connectStatusPending = false;

            function stopConnectStatusPolling() {
                if (connectStatusTimer) {
                    window.clearInterval(connectStatusTimer);
                    connectStatusTimer = null;
                }
                connectStatusPending = false;
            }

            function startConnectStatusPolling(device) {
                stopConnectStatusPolling();

                connectStatusTimer = window.setInterval(function () {
                    if (connectStatusPending) {
                        return;
                    }

                    connectStatusPending = true;
                    $.ajax({
                        url: @json(url('/whatsapp/devices')) + '/' + encodeURIComponent(device) + '/status',
                        method: 'GET',
                        dataType: 'json',
                        headers: {
                            'Accept': 'application/json'
                        }
                    }).done(function (response) {
                        if (!response.connected || !$('#connect-device-modal').hasClass('in')) {
                            return;
                        }

                        stopConnectStatusPolling();
                        $('#connect-device-loading, #connect-device-error, #connect-device-qr-wrapper').hide();
                        $('#connect-device-connected').text(response.message).show();

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    }).always(function () {
                        connectStatusPending = false;
                    });
                }, 3000);
            }

            function connectDevice(device) {
                var $modal = $('#connect-device-modal');
                var $loading = $('#connect-device-loading');
                var $error = $('#connect-device-error');
                var $connected = $('#connect-device-connected');
                var $qrWrapper = $('#connect-device-qr-wrapper');
                var $qr = $('#connect-device-qr');

                $loading.show();
                $error.hide().text('');
                $connected.hide().text('');
                $qrWrapper.hide();
                $qr.removeAttr('src');
                $modal.modal('show');

                $.ajax({
                    url: @json(url('/whatsapp/devices')) + '/' + encodeURIComponent(device) + '/connect',
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    }
                }).done(function (response) {
                    $loading.hide();

                    if (response.connected) {
                        $connected.text(response.message).show();
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                        return;
                    }

                    var qrSource = response.qr.indexOf('data:image') === 0
                        ? response.qr
                        : 'data:image/png;base64,' + response.qr;

                    $qr.attr('src', qrSource);
                    $qrWrapper.show();
                    startConnectStatusPolling(device);
                }).fail(function (xhr) {
                    var response = xhr.responseJSON || {};
                    $loading.hide();
                    $error.text(response.message || 'QR Code koneksi tidak dapat dibuat.').show();
                });
            }

            $('.btn-connect-device').on('click', function () {
                connectDevice($(this).data('device'));
            });

            $('#connect-device-modal').on('hidden.bs.modal', function () {
                stopConnectStatusPolling();
            });

            @if ($errors->any())
                $('#add-device-modal').modal('show');
            @endif

            @if (session('connect_device'))
                connectDevice(@json((string) session('connect_device')));
            @endif
        });
    </script>
@endsection
