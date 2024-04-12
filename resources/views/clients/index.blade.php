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

    <div class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tabelapadrao" class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th>Nama</th>
									<th>Alamat</th>
									<th>Kota</th>
									<th class="text-center">No Telepon</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach($clients as $client)
									@if($client->id)
										<tr>
                                            <td>{{$client->name}}</td>
                                            <td>{{$client->address}}</td>
                                            <td>{{$client->city->name}}</td>
                                            <td class="text-center">{{$client->phone_number}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($client->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('clients.show', $client->id) }}" title="Detail {{ $client->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('clients.edit', $client->id) }}" title="Edit {{ $client->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('clients.destroy', $client->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $client->name}}" data-toggle="modal" data-target="#modal-delete-{{ $client->id }}"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
									@endif
								@endforeach

							</tbody>
							<tfoot>
								<tr>
									<th>Nama</th>
									<th>Alamat</th>
                                    <th>Kota</th>
									<th class="text-center">No Telepon</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
        @if ($clients->hasPages())
            <div class="box-footer with-border">
                {{ $clients->links() }}
            </div>
        @endif
	</div>

@endsection
