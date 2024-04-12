@extends('layouts.AdminLTE.index')

@section('icon_page', 'users')

@section('title', 'Pelanggan')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('clients.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Pelanggan
        </a>
    </li>

@endsection


@section('content')

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Pelanggan</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Edit Client">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{ route('clients.update', $client->id) }}" method="post">
                {{ csrf_field() }}
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name', $client->name) }}" autofocus>
                            @if($errors->has('name'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="address">Alamat</label>
                            <textarea name="address" id="address" required class="form-control">{{ old('address', $client->address) }}</textarea>
                            @if($errors->has('address'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
                            <label for="city_id">Kota</label>
                            <select name="city_id" id="city_id" class="form-control" data-placeholder="Choose City" required>
                                <option disabled> -- Choose Client -- </option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ $city->id === $client->city_id ? 'selected' : '' }}> {{ $city->name }} </option>
                                @endforeach
                            </select>
                            @if($errors->has('city_id'))
                                <span class="help-block">
                                             <strong>{{ $errors->first('client_id') }}</strong>
                                         </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                            <label for="phone_number">No Telepon</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number" required value="{{ old('phone_number', $client->phone_number) }}" autofocus>
                            @if($errors->has('phone_number'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('phone_number') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-default pull-left" style="margin-right: 15px;">Kembali ke Detail Pelanggan</a>
                        <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <a href="{{ route('clients.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Kembali ke Halaman Pelanggan</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
