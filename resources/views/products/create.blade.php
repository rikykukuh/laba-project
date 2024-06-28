@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Tambah Jenis Produk')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('products.index') }}" class="link_menu_page">
			<i class="fa fa-list-alt"></i> Jenis Produk
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Tambah Jenis Produk</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Jenis Produk">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
					 <form action="{{ route('products.store') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="active" value="1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                                    <label for="type">Tipe</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="1" selected>Service</option>
                                        <option value="0">Barang</option>
                                    </select>
                                    @if($errors->has('type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-price form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                                    <label for="price">Harga</label>
                                    <input type="number" name="price" id="price" class="form-control" placeholder="Harga" value="{{ old('name') }}" autofocus>
                                    @if($errors->has('price'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="name">Nama Jenis Produk</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name') }}" autofocus>
                                    @if($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                               <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-fw fa-plus"></i> Tambah</button>
                                <a href="{{ route('products.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Batalkan</a>
                            </div>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('layout_js')
    <script>
        function togglePriceField() {
            const typeVal = $('#type').val();
            $('.form-price input').val('');
            if (typeVal === '1') {
                $('.form-price').show();
                $('.form-price input').attr('required', true);
            } else {
                $('.form-price').hide();
                $('.form-price input').removeAttr('required');
            }
        }

        $(function(){
            $('select').select2();
            togglePriceField();

            $('#type').change(function() {
                togglePriceField();
            });
        });
    </script>
@endsection
