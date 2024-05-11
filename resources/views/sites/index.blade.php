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

    <div class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tabelapadrao" class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center" style="width: 5%;">Kode</th>
									<th class="text-center" style="width: 15%;">Nama</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach($sites as $site)
									@if($site->id)
										<tr>
                                            <th class="text-center" style="width: 5%;">{{$site->code}}</th>
                                            <td class="text-center" style="width: 15%;">{{$site->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($site->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('sites.show', $site->id) }}" title="Detail {{ $site->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('sites.edit', $site->id) }}" title="Edit {{ $site->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('sites.destroy', $site->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $site->name}}" data-toggle="modal" data-target="#modal-delete-{{ $site->id }}"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
									@endif
								@endforeach

							</tbody>
							<tfoot>
								<tr>
									<th>Nama</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
        @if ($sites->hasPages())
        <div class="box-footer with-border">
            {{ $sites->links() }}
        </div>
        @endif
	</div>

@endsection
