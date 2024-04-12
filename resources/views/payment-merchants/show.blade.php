@extends('layouts.AdminLTE.index')

@section('icon_page', 'building')

@section('title', 'Penyedia Pembayaran')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('payment-merchants.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Penyedia Pembayaran
        </a>
    </li>

@endsection


@section('content')

    <div class="col-md-8 col-md-offset-2">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Penyedia Pembayaran</h3>
            </div>

            <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Nama</strong>
                <p>{{ $payment_merchant->name }}</p>
                <hr>
                <strong><i class="fa fa-user-circle-o margin-r-5"></i> Metode Pembayaran</strong>
                <p>{{ $payment_merchant->paymentMethod->name }}</p>
                <hr>
                <strong><i class="fa fa-calendar-o margin-r-5"></i> Tanggal Dibuat</strong>
                <p>{{ $payment_merchant->created_at }}</p>
            </div>

            <div class="box-footer with-border">
                <a href="{{ route('payment-merchants.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Kembai ke Halaman Penyedia Pembayaran</a>
                <a href="{{ route('payment-merchants.edit', $payment_merchant->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Penyedia Pembayaran</a>
                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('payment-merchants.destroy', $payment_merchant->id) }}" method="post" style="display: inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Hapus Penyedia Pembayaran</button>
                </form>
            </div>

        </div>

    </div>

@endsection
