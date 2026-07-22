@extends('layouts.AdminLTE.index')

@section('icon_page', 'user')

@section('title', 'Laporan Teknisi')

@section('menu_pagina')	

@endsection

@section('content')   

@if (session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any() || session('import_errors'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Import gagal:</strong>
        <ul style="margin-bottom: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            @foreach (session('import_errors', []) as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="margin-bottom: 15px;">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-assign-technician">
        <i class="fa fa-user-plus"></i> Penugasan Teknisi
    </button>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Import Penugasan Teknisi</h3>
    </div>
    <div class="box-body">
        <p class="text-muted">
            Download template, pilih teknisi dari dropdown, lalu isi ID Barang, No Bon, dan tanggal pengerjaan.
            Tanggal Dikerjakan wajib diisi. Tanggal Selesai yang kosong otomatis menggunakan tanggal hari ini saat import.
        </p>
        <div class="row">
            <div class="col-md-3">
                <a href="{{ route('order-item-teknisi.import-template') }}" class="btn btn-info btn-block">
                    <i class="fa fa-download"></i> Download Template Excel
                </a>
            </div>
            <div class="col-md-9">
                <form method="POST" action="{{ route('order-item-teknisi.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-upload"></i> Import Excel
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                    <th>Masuk</th>
                    <th>Proses</th>
                    <th>Selesai</th>
                    <th>Gudang A</th>
                    <th>Gudang B</th>
                    <th>Gudang C</th>
                    <th>Cancel</th>
                    <th>Belum Ada State</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $s)
                    <tr>
                        <td>{{ $s->user->name ?? '-' }}</td>
                        <td>{{ $s->total }}</td>
                        <td><span class="label label-info">{{ $s->masuk }}</span></td>
                        <td><span class="label label-warning">{{ $s->proses }}</span></td>
                        <td><span class="label label-success">{{ $s->selesai }}</span></td>
                        <td><span class="label label-primary">{{ $s->gudang_a }}</span></td>
                        <td><span class="label label-primary">{{ $s->gudang_b }}</span></td>
                        <td><span class="label label-primary">{{ $s->gudang_c }}</span></td>
                        <td><span class="label label-danger">{{ $s->cancel }}</span></td>
                        <td><span class="label label-default">{{ $s->belum_ada_state }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data</td>
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

<div class="modal fade" id="modal-assign-technician" tabindex="-1" role="dialog" aria-labelledby="assign-technician-title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="assign-technician-form">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="assign-technician-title">
                        <i class="fa fa-user-plus"></i> Penugasan Teknisi
                    </h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        Pilih teknisi, cari nomor bon, lalu pilih item service yang akan ditangani.
                        Maksimal tiga teknisi dapat ditugaskan pada satu item service.
                    </p>

                    <div id="assignment-message" class="alert" style="display: none;"></div>

                    <div class="form-group">
                        <label for="assignment-user">Teknisi <span class="text-danger">*</span></label>
                        <select id="assignment-user" name="user_id" class="form-control" required>
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach ($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="assignment-order">Nomor Bon <span class="text-danger">*</span></label>
                        <select id="assignment-order" name="order_id" class="form-control" required></select>
                        <p class="help-block">Ketik nomor bon atau nama pelanggan untuk melakukan pencarian.</p>
                    </div>

                    <div class="form-group">
                        <label for="assignment-item">Item Service <span class="text-danger">*</span></label>
                        <select id="assignment-item" name="order_item_id" class="form-control" disabled required>
                            <option value="">-- Pilih bon terlebih dahulu --</option>
                        </select>
                        <p class="help-block">Daftar ini hanya menampilkan item service dari bon yang dipilih.</p>
                    </div>

                    <div class="form-group">
                        <label for="assignment-state">State <span class="text-danger">*</span></label>
                        <select id="assignment-state" name="state" class="form-control" disabled required>
                            <option value="">-- Pilih State --</option>
                            <option value="masuk">Masuk</option>
                            <option value="proses">Proses</option>
                            <option value="selesai">Selesai</option>
                            <option value="gudang A">Gudang A</option>
                            <option value="gudang B">Gudang B</option>
                            <option value="gudang C">Gudang C</option>
                            <option value="cancel">Cancel</option>
                        </select>
                        <p id="assignment-state-help" class="help-block" style="display: none;">
                            Bon sudah diambil, sehingga state otomatis dikunci menjadi Selesai.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" id="assignment-submit" class="btn btn-primary" disabled>
                        <i class="fa fa-save"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(function () {
        var orderItems = {};
        var orderPickedUp = false;
        var allSlotsFull = false;
        var $modal = $('#modal-assign-technician');
        var $technician = $('#assignment-user');
        var $order = $('#assignment-order');
        var $item = $('#assignment-item');
        var $state = $('#assignment-state');
        var $submit = $('#assignment-submit');
        var $message = $('#assignment-message');

        function showAssignmentMessage(type, message) {
            $message
                .removeClass('alert-danger alert-warning alert-success alert-info')
                .addClass('alert-' + type)
                .text(message)
                .show();
        }

        function clearAssignmentMessage() {
            $message.hide().text('');
        }

        function updateFormAvailability() {
            var selectedItem = orderItems[$item.val()];
            var technicianId = parseInt($technician.val(), 10);
            var blocked = false;

            clearAssignmentMessage();

            if (allSlotsFull) {
                showAssignmentMessage('warning', 'Semua slot teknisi pada seluruh item service di bon ini sudah terpenuhi.');
                blocked = true;
            } else if (selectedItem && selectedItem.is_full) {
                showAssignmentMessage('warning', 'Slot teknisi pada item service ini sudah terpenuhi (3/3).');
                blocked = true;
            } else if (selectedItem && technicianId && selectedItem.technician_ids.indexOf(technicianId) !== -1) {
                showAssignmentMessage('warning', 'Teknisi tersebut sudah ditugaskan pada item service ini.');
                blocked = true;
            }

            $submit.prop('disabled', blocked || !$technician.val() || !$order.val() || !$item.val() || !$state.val());
        }

        $technician.select2({
            width: '100%',
            dropdownParent: $modal,
            placeholder: '-- Pilih Teknisi --'
        });

        $order.select2({
            width: '100%',
            dropdownParent: $modal,
            placeholder: 'Cari nomor bon atau pelanggan',
            minimumInputLength: 1,
            ajax: {
                url: @json(route('order-item-teknisi.orders.search')),
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { q: params.term || '' };
                },
                processResults: function (response) {
                    return response;
                },
                cache: true
            }
        });

        $item.select2({
            width: '100%',
            dropdownParent: $modal,
            placeholder: '-- Pilih Item Service --'
        });

        $order.on('change', function () {
            var orderId = $(this).val();
            orderItems = {};
            orderPickedUp = false;
            allSlotsFull = false;
            $item.empty().append(new Option('-- Memuat item service --', '', true, true)).prop('disabled', true).trigger('change');
            $state.val('').prop('disabled', true);
            $('#assignment-state-help').hide();
            $submit.prop('disabled', true);
            clearAssignmentMessage();

            if (!orderId) {
                $item.empty().append(new Option('-- Pilih bon terlebih dahulu --', '', true, true));
                return;
            }

            $.get(@json(url('/laporan/order-item-teknisi/orders')) + '/' + encodeURIComponent(orderId) + '/items')
                .done(function (response) {
                    orderPickedUp = response.order_picked_up;
                    allSlotsFull = response.all_slots_full;
                    $item.empty().append(new Option('-- Pilih Item Service --', '', true, true));

                    response.items.forEach(function (itemData) {
                        itemData.technician_ids = itemData.technician_ids.map(Number);
                        orderItems[String(itemData.id)] = itemData;
                        var label = itemData.text + (itemData.is_full ? ' - SLOT PENUH' : '');
                        var option = new Option(label, itemData.id, false, false);
                        option.disabled = itemData.is_full;
                        $item.append(option);
                    });

                    $item.prop('disabled', response.items.length === 0 || allSlotsFull).trigger('change');

                    if (orderPickedUp) {
                        $state.val('selesai').prop('disabled', true);
                        $('#assignment-state-help').show();
                    } else {
                        $state.val('masuk').prop('disabled', false);
                        $('#assignment-state-help').hide();
                    }

                    if (response.items.length === 0) {
                        showAssignmentMessage('warning', 'Bon ini tidak memiliki item service.');
                    } else {
                        updateFormAvailability();
                    }
                })
                .fail(function () {
                    $item.empty().append(new Option('-- Item service gagal dimuat --', '', true, true));
                    showAssignmentMessage('danger', 'Item service pada bon tidak dapat dimuat. Silakan coba lagi.');
                });
        });

        $item.on('change', function () {
            var selectedItem = orderItems[$(this).val()];

            if (!orderPickedUp) {
                $state.val(selectedItem && selectedItem.state ? selectedItem.state : 'masuk');
            }

            updateFormAvailability();
        });

        $technician.on('change', updateFormAvailability);
        $state.on('change', updateFormAvailability);

        $('#assign-technician-form').on('submit', function (event) {
            event.preventDefault();
            updateFormAvailability();

            if ($submit.prop('disabled')) {
                return;
            }

            $submit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: @json(route('order-item-teknisi.assign')),
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json'
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: $technician.val(),
                    order_id: $order.val(),
                    order_item_id: $item.val(),
                    state: $state.val()
                }
            }).done(function (response) {
                showAssignmentMessage('success', response.message);
                setTimeout(function () {
                    window.location.reload();
                }, 800);
            }).fail(function (xhr) {
                var response = xhr.responseJSON || {};
                var message = response.message || 'Penugasan teknisi gagal disimpan.';

                if (response.errors) {
                    message = Object.keys(response.errors).map(function (key) {
                        return response.errors[key].join(' ');
                    }).join(' ');
                }

                showAssignmentMessage('danger', message);
                $submit.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Penugasan');
            });
        });

        $modal.on('hidden.bs.modal', function () {
            $('#assign-technician-form')[0].reset();
            orderItems = {};
            orderPickedUp = false;
            allSlotsFull = false;
            $technician.val(null).trigger('change');
            $order.val(null).trigger('change');
            $item.empty().append(new Option('-- Pilih bon terlebih dahulu --', '', true, true)).prop('disabled', true).trigger('change');
            $state.val('').prop('disabled', true);
            $('#assignment-state-help').hide();
            $submit.prop('disabled', true).html('<i class="fa fa-save"></i> Simpan Penugasan');
            clearAssignmentMessage();
        });
    });
</script>
@endsection
