@extends('layouts.AdminLTE.index')

@section('icon_page', 'exchange')

@section('title', 'Metode Pembayaran')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('payment-methods.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Metode Pembayaran
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-8 col-md-offset-2">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Metode Pembayaran</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Nama</strong>
                <p>{{ $payment_method->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Tanggal Dibuat</strong>
                <p>{{ $payment_method->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('payment-methods.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Kembali ke Metode Pembayaran</a>
                <a href="{{ route('payment-methods.edit', $payment_method->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Metode Pembayaran</a>
                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('payment-methods.destroy', $payment_method->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Hapus Metode Pembayaran</button>
                </form>
            </div>

        </div>

    </div>

@endsection
