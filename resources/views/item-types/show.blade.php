@extends('layouts.AdminLTE.index')

@section('icon_page', 'list-alt')

@section('title', 'Jenis Barang')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('item-types.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Jenis Barang
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-7 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Jenis Barang</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Nama</strong>
                <p>{{ $item_type->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Tanggal Dibuat</strong>
                <p>{{ $item_type->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('item-types.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Kembali ke Detail Jenis Barang</a>
                <a href="{{ route('item-types.edit', $item_type->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Jenis Barang</a>
                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('item-types.destroy', $item_type->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Hapus Jenis Barang</button>
                </form>
            </div>

        </div>

    </div>

@endsection
