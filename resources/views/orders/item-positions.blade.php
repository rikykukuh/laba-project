@extends('layouts.AdminLTE.index')

@section('icon_page', 'map-marker')

@section('title', 'List Posisi Barang')

@section('menu_pagina')
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Filter Posisi Barang</h3>
        </div>
        <div class="box-body">
            <form method="GET" action="{{ route('orders.item-positions') }}" id="form-filter">
                <input type="hidden" name="date_start" id="date_start" value="{{ $date_start }}">
                <input type="hidden" name="date_end" id="date_end" value="{{ $date_end }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="ALL" {{ $status === 'ALL' ? 'selected' : '' }}>All</option>
                                <option value="DIPROSES" {{ $status === 'DIPROSES' ? 'selected' : '' }}>Diproses</option>
                                <option value="READY" {{ $status === 'READY' ? 'selected' : '' }}>Ready</option>
                                <option value="DIAMBIL" {{ $status === 'DIAMBIL' ? 'selected' : '' }}>Diambil</option>
                                <option value="LUNAS" {{ $status === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="site_id">Cabang</label>
                            <select class="form-control" id="site_id" name="site_id">
                                <option value="ALL" {{ $site_id === 'ALL' ? 'selected' : '' }}>Semua Cabang</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}" {{ $site_id == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Estimasi</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control pull-right" id="reservation">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="state">State Barang</label>
                            <select class="form-control" id="state" name="state">
                                <option value="ALL" {{ $selectedState === 'ALL' ? 'selected' : '' }}>All</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state }}" {{ $selectedState === $state ? 'selected' : '' }}>
                                        {{ ucwords($state) }}
                                    </option>
                                @endforeach
                                @if ($hasItemsWithoutState)
                                    <option value="BELUM_ADA_STATE" {{ $selectedState === 'BELUM_ADA_STATE' ? 'selected' : '' }}>
                                        Belum Ada State
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="search">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ $search }}" placeholder="Cari No Bon, ID Barang, atau keterangan">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Daftar Posisi Barang</h3>
            <div class="pull-right" style="margin-left: 10px;">
                <a href="{{ route('orders.item-positions.print', array_filter(request()->query())) }}" target="_blank" class="btn btn-sm bg-maroon">
                    <i class="fa fa-print"></i> Print PDF
                </a>
            </div>
            <span class="label label-default pull-right">{{ $items->total() }} barang</span>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover table-bordered">
                <thead class="bg-navy">
                    <tr>
                        <th class="text-center">Tanggal Transaksi</th>
                        <th class="text-center">Tanggal Estimasi</th>
                        <th class="text-center">No Bon</th>
                        <th class="text-center">ID Barang</th>
                        <th>Keterangan</th>
                        <th class="text-center">State Barang</th>
                        <th class="text-center">Foto</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        @php
                            $photo = $item->orderItemPhotos->first();
                            $photoUrls = $item->orderItemPhotos->map(function ($itemPhoto) {
                                return asset('storage/' . ltrim($itemPhoto->preview_url ?: $itemPhoto->thumbnail_url, '/'));
                            })->values();
                            $stateClasses = [
                                'masuk' => 'label-info',
                                'proses' => 'label-warning',
                                'selesai' => 'label-success',
                                'gudang A' => 'label-primary',
                                'gudang B' => 'label-primary',
                                'gudang C' => 'label-primary',
                                'cancel' => 'label-danger',
                            ];
                        @endphp
                        <tr>
                            <td class="text-center">
                                {{ optional($item->order)->created_at ? \Carbon\Carbon::parse($item->order->created_at)->timezone('Asia/Jakarta')->format('d-M-Y') : '-' }}
                            </td>
                            <td class="text-center">
                                {{ optional($item->order)->estimate_take_item ? \Carbon\Carbon::parse($item->order->estimate_take_item)->format('d-M-Y') : '-' }}
                            </td>
                            <td class="text-center">{{ optional($item->order)->number_ticket ?? '-' }}</td>
                            <td class="text-center"><strong>{{ $item->id }}</strong></td>
                            <td>{{ $item->note ?: '-' }}</td>
                            <td class="text-center">
                                <span class="label {{ $stateClasses[$item->state] ?? 'label-default' }}">
                                    {{ $item->state ? ucwords($item->state) : 'Belum Ada State' }}
                                </span>
                            </td>
                            <td class="text-center" style="min-width: 110px;">
                                @if ($photo)
                                    <button type="button" class="btn btn-link view-item-photos" style="padding: 0;"
                                        data-toggle="modal" data-target="#item-photos-modal"
                                        data-item-id="{{ $item->id }}" data-photos='@json($photoUrls)'
                                        title="Lihat semua foto barang">
                                        <img src="{{ asset('storage/' . ltrim($photo->thumbnail_url, '/')) }}"
                                            alt="Foto barang {{ $item->id }}" class="img-thumbnail" style="height: 60px;">
                                    </button>
                                    @if ($item->orderItemPhotos->count() > 1)
                                        <span class="label label-info">+{{ $item->orderItemPhotos->count() - 1 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('orders.show', $item->order_id) }}" class="btn btn-primary btn-sm"
                                    title="Lihat detail bon">
                                    <i class="fa fa-eye"></i> Detail Bon
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding: 30px;">
                                Data barang tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="box-footer clearfix">
                <div class="pull-right">{{ $items->links() }}</div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="item-photos-modal" tabindex="-1" role="dialog" aria-labelledby="item-photos-title">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="item-photos-title">Foto Barang</h4>
                </div>
                <div class="modal-body">
                    <div class="row" id="item-photos-gallery"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="item-photo-preview-modal" tabindex="-1" role="dialog" aria-labelledby="item-photo-preview-title">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="item-photo-preview-title">Perbesar Foto Barang</h4>
                </div>
                <div class="modal-body text-center" style="background: #222;">
                    <img id="item-photo-preview-image" src="" alt="Foto barang ukuran besar"
                        class="img-responsive" style="max-height: 75vh; margin: 0 auto;">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('layout_js')
    <script>
        $(function() {
            function initFilters() {
                if (typeof $.fn.select2 === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
                    setTimeout(initFilters, 100);
                    return;
                }

                const date_start = "{{ $date_start }}";
                const date_end = "{{ $date_end }}";

                $('#status').select2();
                $('#site_id').select2();

                $('#reservation').daterangepicker({
                    startDate: date_start !== '' ? moment(date_start).startOf('day') : moment().startOf('day'),
                    endDate: date_end !== '' ? moment(date_end).endOf('day') : moment().endOf('day'),
                    timePickerIncrement: 1,
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    autoUpdateInput: false
                }, function(start, end, label) {
                    $('#date_start').val(start.format('YYYY-MM-DD'));
                    $('#date_end').val(end.format('YYYY-MM-DD'));
                });

                if (date_start !== '' && date_end !== '') {
                    $('#reservation').val(date_start + ' - ' + date_end);
                }

                $('#reservation').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                    $('#date_start').val(picker.startDate.format('YYYY-MM-DD'));
                    $('#date_end').val(picker.endDate.format('YYYY-MM-DD'));
                });

                $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    $('#date_start').val('');
                    $('#date_end').val('');
                });
            }

            initFilters();
        });

        $(document).on('click', '.view-item-photos', function () {
            const itemId = $(this).data('item-id');
            const photos = $(this).data('photos') || [];
            const gallery = $('#item-photos-gallery');

            $('#item-photos-title').text('Foto Barang ID ' + itemId);
            gallery.empty();

            photos.forEach(function (photoUrl, index) {
                gallery.append(
                    '<div class="col-sm-6 col-md-4" style="margin-bottom: 15px;">' +
                        '<button type="button" class="btn btn-link enlarge-item-photo" data-photo-url="' + photoUrl + '" ' +
                            'style="width: 100%; padding: 0;" title="Perbesar foto">' +
                            '<img src="' + photoUrl + '" class="img-responsive img-thumbnail" ' +
                                'alt="Foto barang ' + (index + 1) + '" style="width: 100%; height: 220px; object-fit: contain;">' +
                        '</button>' +
                        '<p class="text-center text-muted" style="margin-top: 5px;">Foto ' + (index + 1) + '</p>' +
                    '</div>'
                );
            });
        });

        $(document).on('click', '.enlarge-item-photo', function () {
            $('#item-photo-preview-image').attr('src', $(this).data('photo-url'));
            $('#item-photo-preview-modal').modal('show');
        });

        $('#item-photo-preview-modal').on('shown.bs.modal', function () {
            $(this).css('z-index', 1070);
            $('.modal-backdrop').last().css('z-index', 1060);
        });

        $('#item-photo-preview-modal').on('hidden.bs.modal', function () {
            $('#item-photo-preview-image').attr('src', '');
            if ($('#item-photos-modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    </script>
@endsection
