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

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Cabang</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Site">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('sites.update', $site->id) }}" method="post" autocomplete="off">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nama Cabang</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $site->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                            <label for="code">Kode Cabang</label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="Kode Cabang" required value="{{ old('code', $site->code) }}" autofocus>
                            @if($errors->has('code'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('code') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('sites.show', $site->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Kembali ke Detail Cabang</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <a href="{{ route('sites.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Kembali ke Halaman Cabang</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
