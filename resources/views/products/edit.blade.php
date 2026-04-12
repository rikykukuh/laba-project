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

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Jenis Produk</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Jenis Produk">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('products.update', $product->id) }}" method="post">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nama Jenis Produk</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $product->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                            <label for="price">Harga Produk</label>
                            <input type="number" name="price" id="price" class="form-control" placeholder="Masukkan harga produk" required value="{{ old('price', (int)$product->price) }}" autofocus>
                            @if($errors->has('price'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('price') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Kembali ke Detail Jenis Produk</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <a href="{{ route('products.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Kembali ke Halaman Jenis Produk</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
