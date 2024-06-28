@extends('layouts.AdminLTE.index')

@section('icon_page', 'list-alt')

@section('title', 'Jenis Produk')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('products.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Jenis Produk
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-7 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Jenis Produk</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Nama</strong>
                <p>{{ $product->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Tanggal Dibuat</strong>
                <p>{{ $product->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('products.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Kembali ke Detail Jenis Produk</a>
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Jenis Produk</a>
                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('products.destroy', $product->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Hapus Jenis Produk</button>
                </form>
            </div>

        </div>

    </div>

@endsection
