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

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Jenis Barang</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Jenis Barang">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('item-types.update', $item_type->id) }}" method="post">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nama Jenis Barang</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $item_type->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('item-types.show', $item_type->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Kembali ke Detail Jenis Barang</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <a href="{{ route('item-types.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Kembali ke Halaman Jenis Barang</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
