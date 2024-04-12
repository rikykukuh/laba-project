@extends('layouts.AdminLTE.index')

@section('icon_page', 'list-ol')

@section('title', 'Cabang')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('sites.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Cabang
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-6 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Cabang</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Nama Cabang</strong>
                <p>{{ $site->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Tanggal Dibuat</strong>
                <p>{{ $site->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('sites.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Kembali ke Halaman Cabang</a>
                <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Cabang</a>
                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('sites.destroy', $site->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Hapus Cabang</button>
                </form>
            </div>

        </div>

    </div>

@endsection
