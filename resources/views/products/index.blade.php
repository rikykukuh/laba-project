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

    <div class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tabelapadrao" class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th>Nama</th>
									<th class="text-center">Tanggal Dibuat</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach($products as $product)
									@if($product->id)
										<tr>
                                            <td>{{$product->name}}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($product->created_at)->timezone('Asia/Jakarta')->toDateTimeString() }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-default  btn-xs" href="{{ route('products.show', $product->id) }}" title="Detail {{ $product->name }}"><i class="fa fa-eye">   </i></a>
                                                <a class="btn btn-warning  btn-xs" href="{{ route('products.edit', $product->id) }}" title="Edit {{ $product->name }}"><i class="fa fa-pencil"></i></a>
                                                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('products.destroy', $product->id) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit" title="Delete {{ $product->name}}" data-toggle="modal" data-target="#modal-delete-{{ $product->id }}"><i class="fa fa-trash"></i></button>
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
        @if ($products->hasPages())
        <div class="box-footer with-border">
            {{ $products->links() }}
        </div>
        @endif
	</div>

@endsection
