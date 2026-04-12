@extends('layouts.AdminLTE.index')

@section('icon_page', 'map-marker')

@section('title', 'Kota')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('cities.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Kota
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-6 col-md-offset-3">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Kota</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Nama</strong>
                <p>{{ $city->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Tanggal Dibuat</strong>
                <p>{{ $city->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('cities.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Kembali ke Halaman Kota</a>
                <a href="{{ route('cities.edit', $city->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Kota</a>
                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('cities.destroy', $city->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Hapus Kota</button>
                </form>
            </div>

        </div>

    </div>

@endsection
