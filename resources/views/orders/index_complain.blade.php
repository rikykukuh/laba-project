@extends('layouts.AdminLTE.index')

@section('icon_page', 'user')

@section('title', 'Users')

@section('menu_pagina')	

@endsection

@section('content')   

<div class="box">
    <div class="box-header">
        <h3 class="box-title">List Complain</h3>

        <div class="box-tools">
            <form method="GET" action="{{ route('laporan.complain-list') }}" style="display:flex; gap:8px;">
                
                <!-- FILTER CUSTOMER -->
                <select name="customer_id" id="customer_id" class="form-control input-sm"></select>

                <!-- SEARCH -->
                <input type="text" name="search" class="form-control input-sm"
                    placeholder="Cari nama / no hp"
                    value="{{ request('search') }}">

                <button type="submit" class="btn btn-default btn-sm">
                    <i class="fa fa-search"></i>
                </button>

                <a href="{{ route('laporan.complain-list') }}" class="btn btn-default btn-sm">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Customer (No HP)</th>
                    <th>Complain</th>
                    <th>Order</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td style="max-width:250px;">
                            {{ optional($item->customer)->name ?? '-' }} 
                            <small>( {{ optional($item->customer)->phone_number ?? '-' }} )</small>
                        </td>
                        <td class="complain-cell" 
                            data-full="{{ $item->complain }}"
                            style="cursor:pointer; max-width:250px;">
                            
                            {{ Str::limit($item->complain, 120) }}
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $item->id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> {{ $item->number_ticket }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data complain</td>
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


<!-- 1. jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- 2. Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<!-- 3. Script kamu -->
<script>
$(document).ready(function () {
    $('#customer_id').select2({
        placeholder: 'Pilih Customer',
        width: '300px',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{ route('customer.search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name + ' (' + (item.phone_number ?? '-') + ')'
                        };
                    })
                };
            }
        }
    });
});

$(document).on('click', '.complain-cell', function () {
    let fullText = $(this).data('full');
    let shortText = fullText.length > 120 
        ? fullText.substring(0, 120) + '...' 
        : fullText;

    if ($(this).hasClass('expanded')) {
        $(this).removeClass('expanded');
        $(this).text(shortText);
    } else {
        $(this).addClass('expanded');
        $(this).text(fullText);
    }
});
</script>


