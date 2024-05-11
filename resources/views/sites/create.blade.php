@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Tambah Cabang')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('sites.index') }}" class="link_menu_page">
			<i class="fa fa-list-ol"></i> Cabang
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Tambah Cabang</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Site">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
					 <form action="{{ route('sites.store') }}" method="post" autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="active" value="1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="name">Nama Cabang</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name') }}" autofocus>
                                    @if($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                    <label for="name">Code Site</label>
                                    <input type="text" name="code" id="code" class="form-control" placeholder="Code Site For Ticket" required value="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                               <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-fw fa-plus"></i> Tambah</button>
                                <a href="{{ route('sites.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Batalkan</a>
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
        $(function(){
            $('.select2').select2({
                "language": {
                    "noResults": function(){
                        return "Nenhum registro encontrado.";
                    }
                }
            });
        });

    </script>

@endsection
