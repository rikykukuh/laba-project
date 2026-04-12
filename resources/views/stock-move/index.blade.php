@extends('layouts.AdminLTE.index')

@section('icon_page', 'list-alt')

@section('title', 'Perpindahan Barang')

@section('menu_pagina')


@endsection


@section('content')

    <div class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tabelapadrao" class="table table-condensed table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th class="text-center">Nama</th>
                                    <th class="text-center">move type</th>
                                    <th class="text-center">Qty</th>
									<th class="text-center">Tanggal</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
                                    <td>Product A</td>
                                    <td>In (masuk)</td>
                                    <td>10</td>
                                    <td>01-02-2025</td>
                                    <td class="text-center">
                                        <a class="btn btn-default  btn-xs" href="#" title="Detail name"><i class="fa fa-eye">   </i></a>
                                        <a class="btn btn-warning  btn-xs" href="#" title="Edit name"><i class="fa fa-pencil"></i></a>
                                        <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="#" method="post" style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs" type="submit" title="Delete name" data-toggle="modal" data-target="#modal-delete-id"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Product B</td>
                                    <td>Out (keluar)</td>
                                    <td>10</td>
                                    <td>01-02-2025</td>
                                    <td class="text-center">
                                        <a class="btn btn-default  btn-xs" href="#" title="Detail name"><i class="fa fa-eye">   </i></a>
                                        <a class="btn btn-warning  btn-xs" href="#" title="Edit name"><i class="fa fa-pencil"></i></a>
                                        <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="#" method="post" style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs" type="submit" title="Delete name" data-toggle="modal" data-target="#modal-delete-id"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Product C</td>
                                    <td>In (masuk)</td>
                                    <td>10</td>
                                    <td>01-02-2025</td>
                                    <td class="text-center">
                                        <a class="btn btn-default  btn-xs" href="#" title="Detail name"><i class="fa fa-eye">   </i></a>
                                        <a class="btn btn-warning  btn-xs" href="#" title="Edit name"><i class="fa fa-pencil"></i></a>
                                        <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="" method="post" style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs" type="submit" title="Delete name" data-toggle="modal" data-target="#modal-delete-id"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Product D</td>
                                    <td>In (masuk)</td>
                                    <td>15</td>
                                    <td>01-04-2025</td>
                                    <td class="text-center">
                                        <a class="btn btn-default  btn-xs" href="#" title="Detail name"><i class="fa fa-eye">   </i></a>
                                        <a class="btn btn-warning  btn-xs" href="#" title="Edit name"><i class="fa fa-pencil"></i></a>
                                        <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="#" method="post" style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs" type="submit" title="Delete name" data-toggle="modal" data-target="#modal-delete-id"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

							</tbody>
							<tfoot>
								<tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">move type</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
