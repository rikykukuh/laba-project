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

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Metode Pembayaran</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Payment">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('payment-methods.update', $payment_method->id) }}" method="post">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nama Metode Pembayaran</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $payment_method->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('payment-methods.show', $payment_method->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Kembali ke Detail Metode Pembayaran</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <a href="{{ route('payment-methods.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Kembali ke Halaman Metode Pembayaran</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
